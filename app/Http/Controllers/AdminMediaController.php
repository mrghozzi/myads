<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminMediaController extends Controller
{
    /**
     * Cache key for media scan
     */
    protected const CACHE_KEY = 'admin_media_files_v2';
    protected const CACHE_TTL = 300; // 5 minutes

    public function index(Request $request)
    {
        // Fetch or scan files from cache
        $allScannedFiles = Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            return $this->scanDirectories();
        });

        // Compute overall storage stats BEFORE filtering
        $stats = $this->getStorageStats($allScannedFiles);

        $files = $allScannedFiles;

        // Directory filter
        if ($request->has('directory') && !empty($request->directory) && $request->directory !== 'all') {
            $dirFilter = $request->directory;
            $files = array_filter($files, function ($file) use ($dirFilter) {
                return $file['directory'] === $dirFilter;
            });
        }

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = strtolower(trim($request->search));
            $files = array_filter($files, function ($file) use ($search) {
                return str_contains(strtolower($file['name']), $search) || str_contains(strtolower($file['path']), $search);
            });
        }

        // Type filter
        if ($request->has('type') && !empty($request->type) && $request->type !== 'all') {
            $type = strtolower($request->type);
            $files = array_filter($files, function ($file) use ($type) {
                if ($type === 'image') {
                    return $file['is_image'];
                }
                if ($type === 'video') {
                    return $file['is_video'];
                }
                if ($type === 'audio') {
                    return $file['is_audio'];
                }
                if ($type === 'archive') {
                    return $file['is_archive'];
                }
                if ($type === 'document') {
                    return $file['is_document'];
                }
                if ($type === 'code') {
                    return $file['is_code'];
                }
                return $file['extension'] === $type;
            });
        }

        // Sort option
        $sort = $request->input('sort', 'newest');
        usort($files, function ($a, $b) use ($sort) {
            switch ($sort) {
                case 'oldest':
                    return $a['last_modified'] <=> $b['last_modified'];
                case 'size_desc':
                    return $b['size_bytes'] <=> $a['size_bytes'];
                case 'size_asc':
                    return $a['size_bytes'] <=> $b['size_bytes'];
                case 'name_asc':
                    return strnatcasecmp($a['name'], $b['name']);
                case 'name_desc':
                    return strnatcasecmp($b['name'], $a['name']);
                case 'newest':
                default:
                    return $b['last_modified'] <=> $a['last_modified'];
            }
        });

        // Pagination
        $perPage = 18;
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $offset = ($currentPage - 1) * $perPage;
        
        $totalFilteredFiles = count($files);
        $pagedFiles = array_slice($files, $offset, $perPage);
        
        $paginatedFiles = new LengthAwarePaginator(
            $pagedFiles, 
            $totalFilteredFiles, 
            $perPage, 
            $currentPage, 
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin::admin.media.index', [
            'files' => $paginatedFiles,
            'total_count' => $totalFilteredFiles,
            'stats' => $stats,
        ]);
    }

    /**
     * Upload single or multiple files directly from media manager
     */
    public function upload(Request $request)
    {
        $request->validate([
            'files' => 'required',
            'files.*' => 'file|max:51200', // max 50MB per file
            'target_dir' => 'nullable|string|in:public_upload,upload',
        ]);

        $targetKey = $request->input('target_dir', 'public_upload');
        $targetPath = $targetKey === 'upload' ? base_path('upload') : public_path('upload');

        if (!File::exists($targetPath)) {
            File::makeDirectory($targetPath, 0755, true);
        }

        $uploadedFiles = $request->file('files');
        if (!is_array($uploadedFiles)) {
            $uploadedFiles = [$uploadedFiles];
        }

        $forbiddenExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phps', 'cgi', 'pl', 'exe', 'sh', 'bat'];
        $uploadedCount = 0;

        foreach ($uploadedFiles as $file) {
            if (!$file->isValid()) {
                continue;
            }

            $ext = strtolower($file->getClientOriginalExtension());
            if (in_array($ext, $forbiddenExtensions)) {
                return redirect()->back()->with('error', 'Security error: Executable files are not allowed for upload.');
            }

            $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $slugName = Str::slug($filename);
            if (empty($slugName)) {
                $slugName = 'media_' . time();
            }
            
            $finalName = $slugName . '_' . Str::random(6) . '.' . $ext;

            $file->move($targetPath, $finalName);
            $uploadedCount++;
        }

        $this->forgetMediaCache();

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => $uploadedCount . ' file(s) uploaded successfully.',
            ]);
        }

        return redirect()->back()->with('success', $uploadedCount . ' file(s) uploaded successfully.');
    }

    public function rename(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'new_name' => ['required', 'string', 'regex:/^[a-zA-Z0-9_\-\.]+$/'],
        ]);

        // SECURITY: Block path traversal in new filename
        $newName = $request->new_name;
        if (str_contains($newName, '..') || str_contains($newName, '/') || str_contains($newName, '\\')) {
            return redirect()->back()->with('error', 'Invalid filename.');
        }

        $oldPath = base_path($request->path);
        $directory = dirname($oldPath);
        $newPath = $directory . DIRECTORY_SEPARATOR . $newName;

        // SECURITY: Validate that both old and new paths are within allowed upload directories
        if (!$this->isWithinAllowedDirectory($oldPath) || !$this->isWithinAllowedDirectory($newPath)) {
            return redirect()->back()->with('error', 'Access denied: path is outside allowed directories.');
        }

        if (!File::exists($oldPath)) {
            return redirect()->back()->with('error', __('messages.no_results'));
        }

        if (File::exists($newPath)) {
            return redirect()->back()->with('error', 'A file with this name already exists.');
        }

        try {
            File::move($oldPath, $newPath);
            $this->forgetMediaCache();
            return redirect()->back()->with('success', __('messages.media_renamed') ?? 'File renamed successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', __('messages.media_rename_failed') ?? 'Failed to rename file.');
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
        ]);

        $filePath = base_path($request->path);

        // SECURITY: Validate that the path is within allowed upload directories
        if (!$this->isWithinAllowedDirectory($filePath)) {
            return redirect()->back()->with('error', 'Access denied: path is outside allowed directories.');
        }

        if (File::exists($filePath)) {
            try {
                File::delete($filePath);
                $this->forgetMediaCache();
                return redirect()->back()->with('success', __('messages.media_deleted') ?? 'File deleted successfully.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('messages.media_delete_failed') ?? 'Failed to delete file.');
            }
        }

        return redirect()->back()->with('error', __('messages.no_results'));
    }

    /**
     * Bulk Delete multiple selected files
     */
    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'paths' => 'required|array',
            'paths.*' => 'string',
        ]);

        $deletedCount = 0;
        foreach ($request->paths as $relPath) {
            $fullPath = base_path($relPath);
            if ($this->isWithinAllowedDirectory($fullPath) && File::exists($fullPath)) {
                try {
                    File::delete($fullPath);
                    $deletedCount++;
                } catch (\Exception $e) {
                    // continue deleting others
                }
            }
        }

        $this->forgetMediaCache();

        return redirect()->back()->with('success', $deletedCount . ' file(s) deleted successfully.');
    }

    /**
     * Force clear media scanner cache
     */
    public function clearCache()
    {
        $this->forgetMediaCache();
        return redirect()->back()->with('success', 'Media cache cleared and index refreshed.');
    }

    private function forgetMediaCache()
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Scan disk directories for media files
     */
    private function scanDirectories(): array
    {
        $directories = [
            'upload' => base_path('upload'),
            'public_upload' => public_path('upload'),
        ];

        $files = [];

        foreach ($directories as $key => $path) {
            if (File::exists($path)) {
                $allFiles = File::allFiles($path);
                foreach ($allFiles as $file) {
                    $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getRealPath());
                    $extension = strtolower($file->getExtension());
                    
                    $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                    $isVideo = in_array($extension, ['mp4', 'webm', 'ogg', 'mov', 'avi', 'mkv']);
                    $isAudio = in_array($extension, ['mp3', 'wav', 'ogg', 'm4a', 'flac', 'aac']);
                    $isArchive = in_array($extension, ['zip', 'rar', '7z', 'gz', 'tar']);
                    $isDocument = in_array($extension, ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt']);
                    $isCode = in_array($extension, ['php', 'js', 'css', 'html', 'json', 'sql', 'md']);

                    $files[] = [
                        'name' => $file->getFilename(),
                        'size' => $this->formatBytes($file->getSize()),
                        'size_bytes' => $file->getSize(),
                        'extension' => $extension,
                        'path' => $relativePath,
                        'full_path' => $file->getRealPath(),
                        'directory' => $key,
                        'last_modified' => $file->getMTime(),
                        'icon' => $this->getFileIcon($extension),
                        'url' => $this->getFileUrl($file->getRealPath()),
                        'is_image' => $isImage,
                        'is_video' => $isVideo,
                        'is_audio' => $isAudio,
                        'is_archive' => $isArchive,
                        'is_document' => $isDocument,
                        'is_code' => $isCode,
                    ];
                }
            }
        }

        return $files;
    }

    /**
     * Compute breakdown storage analytics
     */
    private function getStorageStats(array $files): array
    {
        $totalBytes = 0;
        $totalFiles = count($files);

        $imagesBytes = 0;
        $imagesCount = 0;

        $videosBytes = 0;
        $videosCount = 0;

        $audioBytes = 0;
        $audioCount = 0;

        $archivesBytes = 0;
        $archivesCount = 0;

        $documentsBytes = 0;
        $documentsCount = 0;

        foreach ($files as $file) {
            $bytes = $file['size_bytes'];
            $totalBytes += $bytes;

            if ($file['is_image']) {
                $imagesBytes += $bytes;
                $imagesCount++;
            } elseif ($file['is_video']) {
                $videosBytes += $bytes;
                $videosCount++;
            } elseif ($file['is_audio']) {
                $audioBytes += $bytes;
                $audioCount++;
            } elseif ($file['is_archive']) {
                $archivesBytes += $bytes;
                $archivesCount++;
            } else {
                $documentsBytes += $bytes;
                $documentsCount++;
            }
        }

        return [
            'total_files' => $totalFiles,
            'total_size_bytes' => $totalBytes,
            'total_size_formatted' => $this->formatBytes($totalBytes),
            'images_count' => $imagesCount,
            'images_size_formatted' => $this->formatBytes($imagesBytes),
            'videos_count' => $videosCount,
            'videos_size_formatted' => $this->formatBytes($videosBytes),
            'audio_count' => $audioCount,
            'audio_size_formatted' => $this->formatBytes($audioBytes),
            'archives_count' => $archivesCount,
            'archives_size_formatted' => $this->formatBytes($archivesBytes),
            'documents_count' => $documentsCount,
            'documents_size_formatted' => $this->formatBytes($documentsBytes),
        ];
    }

    /**
     * SECURITY: Verify that a given path is within allowed upload directories.
     */
    private function isWithinAllowedDirectory(string $path): bool
    {
        $realPath = realpath($path);
        if ($realPath === false) {
            $parentDir = realpath(dirname($path));
            if ($parentDir === false) {
                return false;
            }
            $realPath = $parentDir . DIRECTORY_SEPARATOR . basename($path);
        }

        $normalizedPath = strtolower(str_replace('\\', '/', $realPath));

        $allowedDirectories = [
            strtolower(str_replace('\\', '/', realpath(base_path('upload')) ?: base_path('upload'))),
            strtolower(str_replace('\\', '/', realpath(public_path('upload')) ?: public_path('upload'))),
        ];

        foreach ($allowedDirectories as $allowedDir) {
            if (str_starts_with($normalizedPath, $allowedDir)) {
                return true;
            }
        }

        return false;
    }

    private function getFileIcon($extension)
    {
        $icons = [
            'jpg' => 'jpg.png',
            'jpeg' => 'jpg.png',
            'png' => 'png.png',
            'gif' => 'png.png',
            'webp' => 'png.png',
            'zip' => 'zip.png',
            'rar' => 'zip.png',
            '7z' => 'zip.png',
            'pdf' => 'pdf.png',
            'mp3' => 'mp3.png',
            'wav' => 'mp3.png',
            'php' => 'php.png',
            'js' => 'js.png',
            'css' => 'css.png',
            'html' => 'html.png',
            'txt' => 'txt.png',
            'sql' => 'sql.png',
            'psd' => 'psd.png',
        ];

        $icon = $icons[$extension] ?? 'undefined.png';
        return admin_asset('admin-duralux/images/file-icons/' . $icon);
    }

    private function getFileUrl($fullPath)
    {
        $basePath = realpath(base_path());
        $publicPath = realpath(public_path());
        $fullPath = realpath($fullPath);

        if (!$fullPath) {
            return null;
        }

        if ($publicPath && str_starts_with(strtolower($fullPath), strtolower($publicPath))) {
            $relativePath = substr($fullPath, strlen($publicPath));
            $relativePath = ltrim($relativePath, DIRECTORY_SEPARATOR);
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
            return asset($relativePath);
        }

        $rootUploadPath = realpath(base_path('upload'));
        if ($rootUploadPath && str_starts_with(strtolower($fullPath), strtolower($rootUploadPath))) {
            $relativePath = substr($fullPath, strlen($basePath));
            $relativePath = ltrim($relativePath, DIRECTORY_SEPARATOR);
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
            return url($relativePath);
        }
        
        return null;
    }

    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}

