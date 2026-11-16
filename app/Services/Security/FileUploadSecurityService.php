<?php

namespace App\Services\Security;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class FileUploadSecurityService
{
    /**
     * Dangerous file extensions that must never be allowed inside uploaded archives or media.
     */
    public const FORBIDDEN_EXTENSIONS = [
        'php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'php8', 'pht', 'phar',
        'cgi', 'pl', 'py', 'asp', 'aspx', 'jsp', 'sh', 'bash',
        'exe', 'com', 'bat', 'cmd', 'vbs', 'scr', 'msi', 'jar',
        'htaccess', 'htpasswd', 'ini', 'env', 'config', 'svg', 'svgz',
    ];

    /**
     * Standard allowed image extensions.
     */
    public const ALLOWED_IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

    /**
     * Standard allowed image MIME types.
     */
    public const ALLOWED_IMAGE_MIMES = [
        'image/jpeg',
        'image/png',
        'image/gif',
        'image/webp',
    ];

    /**
     * Known binary magic bytes signatures.
     */
    protected const MIME_MAGIC_SIGNATURES = [
        'image/jpeg' => ["\xFF\xD8\xFF"],
        'image/png'  => ["\x89PNG\r\n\x1a\n"],
        'image/gif'  => ['GIF87a', 'GIF89a'],
        'image/webp' => ['RIFF'], // Needs RIFF....WEBP check
        'application/zip' => ["PK\x03\x04", "PK\x05\x06", "PK\x07\x08"],
        'application/pdf' => ['%PDF-'],
    ];

    /**
     * Validate the real binary MIME type of a file against an allowed list.
     *
     * @param UploadedFile|string $file
     * @param array $allowedMimes
     * @return bool
     */
    public function validateBinaryMime($file, array $allowedMimes): bool
    {
        $filePath = $file instanceof UploadedFile ? $file->getPathname() : (string) $file;

        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false;
        }

        // 1. Check using UploadedFile method or PHP finfo
        $detectedMime = null;
        if ($file instanceof UploadedFile) {
            try {
                $detectedMime = $file->getMimeType();
            } catch (\Throwable) {
                $detectedMime = null;
            }
        }

        if (!$detectedMime && function_exists('finfo_open')) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            if ($finfo) {
                $detectedMime = @finfo_file($finfo, $filePath) ?: null;
                finfo_close($finfo);
            }
        }

        if (!$detectedMime && function_exists('mime_content_type')) {
            $detectedMime = @mime_content_type($filePath) ?: null;
        }

        if (!$detectedMime) {
            return false;
        }

        // Normalize mime type
        $detectedMime = strtolower(trim($detectedMime));

        // 2. Direct match or wildcard match (e.g. image/*)
        $isAllowed = false;
        foreach ($allowedMimes as $allowed) {
            $allowed = strtolower(trim($allowed));
            if ($allowed === $detectedMime) {
                $isAllowed = true;
                break;
            }
            if (str_ends_with($allowed, '/*')) {
                $prefix = substr($allowed, 0, -1);
                if (str_starts_with($detectedMime, $prefix)) {
                    $isAllowed = true;
                    break;
                }
            }
        }

        if (!$isAllowed) {
            return false;
        }

        // 3. Deep magic bytes verification for critical types
        return $this->verifyMagicBytes($filePath, $detectedMime);
    }

    /**
     * Verify initial byte signatures for the detected MIME type.
     */
    protected function verifyMagicBytes(string $filePath, string $mime): bool
    {
        if (!isset(self::MIME_MAGIC_SIGNATURES[$mime])) {
            return true; // No explicit signature required for this type
        }

        $handle = @fopen($filePath, 'rb');
        if (!$handle) {
            return false;
        }

        $header = fread($handle, 16);
        fclose($handle);

        if ($header === false || strlen($header) < 4) {
            return false;
        }

        if ($mime === 'image/webp') {
            return str_starts_with($header, 'RIFF') && str_contains($header, 'WEBP');
        }

        foreach (self::MIME_MAGIC_SIGNATURES[$mime] as $sig) {
            if (str_starts_with($header, $sig)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Inspect and validate a plugin ZIP archive before extraction.
     * Checks for directory traversal attempts, nested dangerous scripts, and manifest presence.
     *
     * @param string $zipFilePath
     * @return array{valid: bool, error: ?string, manifest: ?array}
     */
    public function validatePluginArchive(string $zipFilePath): array
    {
        if (!file_exists($zipFilePath) || !is_readable($zipFilePath)) {
            return ['valid' => false, 'error' => 'messages.plugin_zip_open_failed', 'manifest' => null];
        }

        $zip = new ZipArchive();
        $openResult = $zip->open($zipFilePath, ZipArchive::CHECKCONS);

        if ($openResult !== true) {
            return ['valid' => false, 'error' => 'messages.plugin_zip_open_failed', 'manifest' => null];
        }

        $manifestContent = null;
        $manifestPath = null;
        $totalFiles = $zip->numFiles;

        for ($i = 0; $i < $totalFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            // Path Traversal Protection
            if (str_contains($filename, '..') || str_starts_with($filename, '/') || str_starts_with($filename, '\\')) {
                $zip->close();
                Log::warning("Security Alert: Malicious path traversal entry in plugin zip: {$filename}");
                return ['valid' => false, 'error' => 'messages.plugin_zip_malicious_path', 'manifest' => null];
            }

            // Forbidden executable extensions protection
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, self::FORBIDDEN_EXTENSIONS, true)) {
                $zip->close();
                Log::warning("Security Alert: Forbidden executable file in plugin zip: {$filename}");
                return ['valid' => false, 'error' => 'messages.plugin_zip_forbidden_file', 'manifest' => null];
            }

            // Check for plugin.json (either root or exactly 1 level down)
            if (basename($filename) === 'plugin.json') {
                $depth = substr_count(trim($filename, '/\\'), '/');
                if ($depth <= 1 && $manifestContent === null) {
                    $manifestPath = $filename;
                    $manifestContent = $zip->getFromIndex($i);
                }
            }
        }

        $zip->close();

        if (empty($manifestContent)) {
            return ['valid' => false, 'error' => 'messages.plugin_zip_structure_invalid', 'manifest' => null];
        }

        $manifestData = json_decode($manifestContent, true);
        if (!is_array($manifestData) || empty($manifestData['slug'])) {
            return ['valid' => false, 'error' => 'messages.plugin_manifest_slug_required', 'manifest' => null];
        }

        // Validate slug characters (alphanumeric and hyphens only)
        if (!preg_match('/^[a-z0-9\-_]+$/i', $manifestData['slug'])) {
            return ['valid' => false, 'error' => 'messages.plugin_invalid_slug_characters', 'manifest' => null];
        }

        return ['valid' => true, 'error' => null, 'manifest' => $manifestData];
    }

    /**
     * Validate an uploaded theme ZIP package against path traversal, forbidden files, and schema.
     *
     * @param string $zipFilePath
     * @return array{valid: bool, error: ?string, manifest: ?array}
     */
    public function validateThemeArchive(string $zipFilePath): array
    {
        if (!file_exists($zipFilePath) || !is_readable($zipFilePath)) {
            return ['valid' => false, 'error' => 'messages.theme_zip_open_failed', 'manifest' => null];
        }

        $zip = new ZipArchive();
        $openResult = $zip->open($zipFilePath, ZipArchive::CHECKCONS);

        if ($openResult !== true) {
            return ['valid' => false, 'error' => 'messages.theme_zip_open_failed', 'manifest' => null];
        }

        $manifestContent = null;
        $manifestPath = null;
        $totalFiles = $zip->numFiles;

        for ($i = 0; $i < $totalFiles; $i++) {
            $filename = $zip->getNameIndex($i);

            // Path Traversal Protection
            if (str_contains($filename, '..') || str_starts_with($filename, '/') || str_starts_with($filename, '\\')) {
                $zip->close();
                Log::warning("Security Alert: Malicious path traversal entry in theme zip: {$filename}");
                return ['valid' => false, 'error' => 'messages.theme_zip_malicious_path', 'manifest' => null];
            }

            // Forbidden executable extensions protection
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            if (in_array($ext, self::FORBIDDEN_EXTENSIONS, true)) {
                $zip->close();
                Log::warning("Security Alert: Forbidden executable file in theme zip: {$filename}");
                return ['valid' => false, 'error' => 'messages.theme_zip_forbidden_file', 'manifest' => null];
            }

            // Check for theme.json (either root or exactly 1 level down)
            if (basename($filename) === 'theme.json') {
                $depth = substr_count(trim($filename, '/\\'), '/');
                if ($depth <= 1 && $manifestContent === null) {
                    $manifestPath = $filename;
                    $manifestContent = $zip->getFromIndex($i);
                }
            }
        }

        $zip->close();

        if (empty($manifestContent)) {
            return ['valid' => false, 'error' => 'messages.theme_zip_structure_invalid', 'manifest' => null];
        }

        $manifestData = json_decode($manifestContent, true);
        if (!is_array($manifestData) || empty($manifestData['slug'])) {
            return ['valid' => false, 'error' => 'messages.theme_manifest_slug_required', 'manifest' => null];
        }

        // Validate slug characters (alphanumeric and hyphens only)
        if (!preg_match('/^[a-z0-9\-_]+$/i', $manifestData['slug'])) {
            return ['valid' => false, 'error' => 'messages.theme_invalid_slug_characters', 'manifest' => null];
        }

        return ['valid' => true, 'error' => null, 'manifest' => $manifestData];
    }

    /**
     * Sanitize an SVG string by stripping dangerous scripts, event handlers, and external entities.
     *
     * @param string $svgContent
     * @return string|false
     */
    public function sanitizeSvg(string $svgContent): string|false
    {
        if (trim($svgContent) === '') {
            return false;
        }

        // Reject if contains XML entity expansion (XXE protection)
        if (preg_match('/<!ENTITY/i', $svgContent) || preg_match('/<!DOCTYPE/i', $svgContent)) {
            return false;
        }

        // Remove <script> tags and contents
        $sanitized = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $svgContent);

        // Remove <foreignObject> tags
        $sanitized = preg_replace('#<foreignObject(.*?)>(.*?)</foreignObject>#is', '', $sanitized);

        // Remove all inline event handlers (onload, onerror, onclick, etc.)
        $sanitized = preg_replace('/\bon\w+\s*=\s*(["\']?[^"\'>\s]+["\']?)/i', '', $sanitized);

        // Remove javascript: and vbscript: URIs
        $sanitized = preg_replace('/href\s*=\s*["\']?\s*(?:javascript|vbscript|data):/i', 'href="#disabled"', $sanitized);
        $sanitized = preg_replace('/xlink:href\s*=\s*["\']?\s*(?:javascript|vbscript|data):/i', 'xlink:href="#disabled"', $sanitized);

        // Verify valid SVG opening tag
        if (!preg_match('/<svg[^>]*>/i', $sanitized)) {
            return false;
        }

        return $sanitized;
    }

    /**
     * Inspect file content for executable PHP, ASP, or shell code signatures.
     * Prevents image polyglot attacks where PHP payloads are appended to valid image files.
     */
    public function containsExecutableCode(string $filePath): bool
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            return false;
        }

        // Read up to 512KB of the file (or full file if smaller)
        $handle = @fopen($filePath, 'rb');
        if (!$handle) {
            return false;
        }

        $sample = fread($handle, 524288);
        fclose($handle);

        if ($sample === false || $sample === '') {
            return false;
        }

        // Patterns indicating embedded server-side executable code
        $dangerousPatterns = [
            '/<\?php/i',
            '/<\?=/i',
            '/<\?\s+/i',
            '/<\x00\?\x00p\x00h\x00p/i', // UTF-16 encoded <?php
            '/<\?\x00p\x00h\x00p/i',
            '/<script\s+language\s*=\s*["\']?php["\']?/i',
            '/__halt_compiler\s*\(/i',
            '/<\%(=|\s+)/i', // ASP tags
        ];

        foreach ($dangerousPatterns as $pattern) {
            if (preg_match($pattern, $sample)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Validate an uploaded image with deep binary inspection, polyglot defense,
     * extension whitelisting, and secure random filename generation.
     *
     * @param mixed $file
     * @param array $allowedExtensions
     * @param string $filenamePrefix
     * @return array{valid: bool, error: ?string, extension: string, filename: string}
     */
    public function validateImageUpload(
        mixed $file,
        array $allowedExtensions = self::ALLOWED_IMAGE_EXTENSIONS,
        string $filenamePrefix = 'img'
    ): array {
        if (!($file instanceof UploadedFile) || !$file->isValid()) {
            return [
                'valid' => false,
                'error' => __('messages.invalid_file_upload') ?: 'Invalid file upload.',
                'extension' => '',
                'filename' => '',
            ];
        }

        $clientExt = strtolower(trim((string) $file->getClientOriginalExtension()));
        $clientName = (string) $file->getClientOriginalName();

        // 1. Immediately block dangerous client extensions or double-extension attacks (e.g. shell.php.jpg, avatar.php)
        if (in_array($clientExt, self::FORBIDDEN_EXTENSIONS, true)) {
            Log::critical("Security Alert: Blocked upload attempt with forbidden extension: {$clientName}");
            return [
                'valid' => false,
                'error' => __('messages.file_type_not_allowed') ?: 'File type not allowed.',
                'extension' => '',
                'filename' => '',
            ];
        }

        if (preg_match('/\.(php|phtml|phar|cgi|pl|py|sh|asp|jsp)\./i', $clientName)) {
            Log::critical("Security Alert: Blocked double extension attack in uploaded file: {$clientName}");
            return [
                'valid' => false,
                'error' => __('messages.file_type_not_allowed') ?: 'File type not allowed.',
                'extension' => '',
                'filename' => '',
            ];
        }

        // 2. Inspect for polyglot embedded PHP or executable payloads
        if ($this->containsExecutableCode($file->getPathname())) {
            Log::critical("Security Alert: Executable code payload detected in uploaded image: {$clientName}");
            return [
                'valid' => false,
                'error' => __('messages.file_type_not_allowed') ?: 'File contains unsafe code.',
                'extension' => '',
                'filename' => '',
            ];
        }

        // 3. Validate binary magic bytes and real MIME type
        if (!$this->validateBinaryMime($file, self::ALLOWED_IMAGE_MIMES)) {
            Log::warning("Security Alert: Invalid MIME magic bytes for image: {$clientName}");
            return [
                'valid' => false,
                'error' => __('messages.invalid_file_type') ?: 'Invalid file format.',
                'extension' => '',
                'filename' => '',
            ];
        }

        // 4. Safely determine extension using server-side MIME deduction, NEVER trust user extension
        $safeExt = strtolower((string) ($file->guessExtension() ?: 'png'));
        if ($safeExt === 'jpeg') {
            $safeExt = 'jpg';
        }

        if (!in_array($safeExt, $allowedExtensions, true)) {
            return [
                'valid' => false,
                'error' => __('messages.invalid_file_type') ?: 'Image extension not permitted.',
                'extension' => '',
                'filename' => '',
            ];
        }

        $filename = $filenamePrefix . '_' . time() . '_' . \Illuminate\Support\Str::random(16) . '.' . $safeExt;

        return [
            'valid' => true,
            'error' => null,
            'extension' => $safeExt,
            'filename' => $filename,
        ];
    }

    /**
     * Validate a general media or document attachment upload.
     *
     * @param mixed $file
     * @param array $allowedExtensions
     * @param string $filenamePrefix
     * @return array{valid: bool, error: ?string, extension: string, filename: string}
     */
    public function validateMediaUpload(
        mixed $file,
        array $allowedExtensions,
        string $filenamePrefix = 'att'
    ): array {
        if (!($file instanceof UploadedFile) || !$file->isValid()) {
            return [
                'valid' => false,
                'error' => __('messages.invalid_file_upload') ?: 'Invalid file upload.',
                'extension' => '',
                'filename' => '',
            ];
        }

        $clientExt = strtolower(trim((string) $file->getClientOriginalExtension()));
        $clientName = (string) $file->getClientOriginalName();

        // 1. Block forbidden extensions
        if (in_array($clientExt, self::FORBIDDEN_EXTENSIONS, true) || preg_match('/\.(php|phtml|phar|cgi|pl|py|sh|asp|jsp)\./i', $clientName)) {
            Log::critical("Security Alert: Blocked upload attempt with dangerous extension: {$clientName}");
            return [
                'valid' => false,
                'error' => __('messages.file_type_not_allowed') ?: 'File type not allowed.',
                'extension' => '',
                'filename' => '',
            ];
        }

        // 2. Ensure extension is in the allowed whitelist
        $deducedExt = strtolower((string) ($file->guessExtension() ?: $clientExt));
        if ($deducedExt === 'jpeg') {
            $deducedExt = 'jpg';
        }

        if (!in_array($clientExt, $allowedExtensions, true) && !in_array($deducedExt, $allowedExtensions, true)) {
            return [
                'valid' => false,
                'error' => __('messages.file_type_not_allowed') ?: 'File extension not allowed.',
                'extension' => '',
                'filename' => '',
            ];
        }

        $safeExt = in_array($deducedExt, $allowedExtensions, true) ? $deducedExt : $clientExt;

        // 3. Scan for executable payloads
        if ($this->containsExecutableCode($file->getPathname())) {
            Log::critical("Security Alert: Executable code payload detected in uploaded attachment: {$clientName}");
            return [
                'valid' => false,
                'error' => __('messages.file_type_not_allowed') ?: 'File contains unsafe code.',
                'extension' => '',
                'filename' => '',
            ];
        }

        $filename = $filenamePrefix . '_' . time() . '_' . \Illuminate\Support\Str::random(16) . '.' . $safeExt;

        return [
            'valid' => true,
            'error' => null,
            'extension' => $safeExt,
            'filename' => $filename,
        ];
    }

    /**
     * Ensure a target upload directory exists and is fortified with an .htaccess file
     * that completely disables PHP and CGI script execution across all engines.
     *
     * @param string $directoryPath
     */
    public static function ensureUploadDirectoryProtected(string $directoryPath): void
    {
        if (!is_dir($directoryPath)) {
            @mkdir($directoryPath, 0755, true);
        }

        $htaccessPath = rtrim($directoryPath, '/\\') . DIRECTORY_SEPARATOR . '.htaccess';
        $desiredContent = self::getUploadDirectoryHtaccessContent();

        if (!file_exists($htaccessPath) || filesize($htaccessPath) < 50) {
            @file_put_contents($htaccessPath, $desiredContent);
        }
    }

    /**
     * Ensure all storage directories (storage, storage/logs, storage/framework) are
     * fortified against direct web access and unauthorized inspection.
     */
    public static function ensureStorageDirectoriesProtected(): void
    {
        $storageRoot = storage_path();
        if (is_dir($storageRoot)) {
            $rootHtaccess = $storageRoot . DIRECTORY_SEPARATOR . '.htaccess';
            if (!file_exists($rootHtaccess)) {
                @file_put_contents($rootHtaccess, self::getStorageRootHtaccessContent());
            }
        }

        $logsDir = storage_path('logs');
        if (is_dir($logsDir)) {
            $logsHtaccess = $logsDir . DIRECTORY_SEPARATOR . '.htaccess';
            if (!file_exists($logsHtaccess)) {
                @file_put_contents($logsHtaccess, "Order Allow,Deny\nDeny from all\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n");
            }
        }

        $frameworkDir = storage_path('framework');
        if (is_dir($frameworkDir)) {
            $frameworkHtaccess = $frameworkDir . DIRECTORY_SEPARATOR . '.htaccess';
            if (!file_exists($frameworkHtaccess)) {
                @file_put_contents($frameworkHtaccess, "Order Allow,Deny\nDeny from all\n<IfModule mod_authz_core.c>\n    Require all denied\n</IfModule>\n");
            }
        }
    }

    /**
     * Comprehensive .htaccess content for public upload directories.
     */
    public static function getUploadDirectoryHtaccessContent(): string
    {
        return <<<'HTACCESS'
# ==============================================================================
# MYADS SECURITY SUITE: Upload Directory Hardening
# Strictly prevents execution of server-side scripts in this directory.
# ==============================================================================

# Disable directory indexing and execution of CGI/scripts
Options -Indexes -ExecCGI

# Remove script handlers
RemoveHandler .php .phtml .php3 .php4 .php5 .php7 .php8 .pht .phar .cgi .pl .py .sh
RemoveType .php .phtml .php3 .php4 .php5 .php7 .php8 .pht .phar .cgi .pl .py .sh

# Disable PHP engine if mod_php is loaded
<IfModule mod_php.c>
    php_flag engine off
</IfModule>
<IfModule mod_php7.c>
    php_flag engine off
</IfModule>
<IfModule mod_php8.c>
    php_flag engine off
</IfModule>

# Deny access to any executable, script, or configuration file formats
<FilesMatch "\.(?i:php|phtml|php3|php4|php5|php7|php8|pht|phar|cgi|pl|py|sh|bash|exe|bat|cmd|vbs|scr|msi|jar|dll|htaccess|htpasswd|ini|env|config|svgz)$">
    Order Allow,Deny
    Deny from all
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</FilesMatch>

# Serve all files with neutral binary/octet-stream if MIME is not recognized
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
</IfModule>
HTACCESS;
    }

    /**
     * .htaccess content for storage root.
     */
    public static function getStorageRootHtaccessContent(): string
    {
        return <<<'HTACCESS'
# ==============================================================================
# MYADS SECURITY SUITE: Storage Root Directory Protection
# Prevents public web browsing and sensitive data leaks from storage.
# ==============================================================================

Options -Indexes

# Block access to logs, SQLite databases, keys, environment, and framework caches
<FilesMatch "\.(?i:log|sqlite|sqlite3|db|env|key|pem|crt|json|lock|yml|yaml|php|sh|sql)$">
    Order Allow,Deny
    Deny from all
    <IfModule mod_authz_core.c>
        Require all denied
    </IfModule>
</FilesMatch>
HTACCESS;
    }
}
