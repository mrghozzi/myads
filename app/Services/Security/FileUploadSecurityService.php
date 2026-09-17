<?php

namespace App\Services\Security;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use ZipArchive;

class FileUploadSecurityService
{
    /**
     * Dangerous file extensions that must never be allowed inside uploaded plugin archives.
     */
    protected const FORBIDDEN_EXTENSIONS = [
        'phar', 'phtml', 'php3', 'php4', 'php5', 'php7', 'pht',
        'cgi', 'pl', 'py', 'asp', 'aspx', 'jsp', 'sh', 'bash',
        'exe', 'com', 'bat', 'cmd', 'vbs', 'scr', 'msi', 'jar',
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

        // 1. Check using PHP finfo
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $detectedMime = finfo_file($finfo, $filePath);
        finfo_close($finfo);

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
}
