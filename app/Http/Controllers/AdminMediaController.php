<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Pagination\LengthAwarePaginator;

class AdminMediaController extends Controller
{
    public function index(Request $request)
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
                        'is_image' => in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']),
                    ];
                }
            }
        }

        // Sort by last modified desc by default
        usort($files, function($a, $b) {
            return $b['last_modified'] <=> $a['last_modified'];
        });

        // Search filter
        if ($request->has('search') && !empty($request->search)) {
            $search = strtolower($request->search);
            $files = array_filter($files, function($file) use ($search) {
                return str_contains(strtolower($file['name']), $search) || str_contains(strtolower($file['path']), $search);
            });
        }

        // Type filter
        if ($request->has('type') && !empty($request->type)) {
            $type = strtolower($request->type);
            $files = array_filter($files, function($file) use ($type) {
                if ($type === 'image') {
                    return in_array($file['extension'], ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg']);
                }
                if ($type === 'video') {
                    return in_array($file['extension'], ['mp4', 'webm', 'ogg', 'mov']);
                }
                if ($type === 'archive') {
                    return in_array($file['extension'], ['zip', 'rar', '7z', 'gz', 'tar']);
                }
                return $file['extension'] === $type;
            });
        }

        // Pagination
        $perPage = 15;
        $currentPage = $request->input('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        
        $totalFiles = count($files);
        $pagedFiles = array_slice($files, $offset, $perPage);
        
        $paginatedFiles = new LengthAwarePaginator(
            $pagedFiles, 
            $totalFiles, 
            $perPage, 
            $currentPage, 
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('admin::admin.media.index', [
            'files' => $paginatedFiles,
            'total_count' => $totalFiles
        ]);
    }

    public function rename(Request $request)
    {
        $request->validate([
            'path' => 'required|string',
            'new_name' => 'required|string',
        ]);

        $oldPath = base_path($request->path);
        $directory = dirname($oldPath);
        $newPath = $directory . DIRECTORY_SEPARATOR . $request->new_name;

        if (!File::exists($oldPath)) {
            return redirect()->back()->with('error', __('messages.no_results'));
        }

        if (File::exists($newPath)) {
            return redirect()->back()->with('error', 'A file with this name already exists.');
        }

        try {
            File::move($oldPath, $newPath);
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

        if (File::exists($filePath)) {
            try {
                File::delete($filePath);
                return redirect()->back()->with('success', __('messages.media_deleted') ?? 'File deleted successfully.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', __('messages.media_delete_failed') ?? 'Failed to delete file.');
            }
        }

        return redirect()->back()->with('error', __('messages.no_results'));
    }

    private function getFileIcon($extension)
    {
        $icons = [
            'jpg' => 'jpg.png',
            'jpeg' => 'jpg.png',
            'png' => 'png.png',
            'gif' => 'png.png', // Fallback
            'webp' => 'png.png', // Fallback
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

        // 1. Check if it's in the public directory (most reliable)
        if ($publicPath && str_starts_with(strtolower($fullPath), strtolower($publicPath))) {
            $relativePath = substr($fullPath, strlen($publicPath));
            $relativePath = ltrim($relativePath, DIRECTORY_SEPARATOR);
            $relativePath = str_replace(DIRECTORY_SEPARATOR, '/', $relativePath);
            return asset($relativePath);
        }

        // 2. Check if it's in the root upload directory (accessible via .htaccess)
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
