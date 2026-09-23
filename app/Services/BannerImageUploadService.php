<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class BannerImageUploadService
{
    /**
     * Allowed image extensions.
     */
    protected const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'];

    /**
     * Maximum file size in kilobytes (5MB).
     */
    protected const MAX_FILE_SIZE_KB = 5120;

    /**
     * Upload directory relative to base path.
     */
    protected const UPLOAD_SUBDIR = 'upload/banners';

    /**
     * Resolve banner image from either uploaded file or URL input.
     *
     * @param Request $request
     * @param string $fileKey Key for file input (e.g. 'img_file' or 'img_file_b')
     * @param string $urlKey Key for text URL input (e.g. 'img' or 'img_b')
     * @param string|null $currentValue Existing image URL if updating
     * @param bool $required Whether an image is strictly required (for create/update)
     * @return string|null The resolved public URL or null
     * @throws ValidationException
     */
    public function resolveImage(
        Request $request,
        string $fileKey = 'img_file',
        string $urlKey = 'img',
        ?string $currentValue = null,
        bool $required = false
    ): ?string {
        // 1. Check if a new file was uploaded
        if ($request->hasFile($fileKey)) {
            $file = $request->file($fileKey);
            if ($file instanceof UploadedFile) {
                return $this->saveUploadedBanner($file, $fileKey);
            }
        }

        // 2. Check if a URL text was provided
        $urlInput = trim((string) $request->input($urlKey, ''));
        if ($urlInput !== '') {
            return $this->normalizeImageUrl($urlInput);
        }

        // 3. If updating and no new file/URL provided, keep current value
        if ($currentValue !== null && trim($currentValue) !== '') {
            return trim($currentValue);
        }

        // 4. If required and nothing provided, throw validation error
        if ($required) {
            $msg = __('messages.banner_image_required') ?: 'Please upload a banner image or provide an image URL.';
            throw ValidationException::withMessages([
                $urlKey => [$msg],
            ]);
        }

        return null;
    }

    /**
     * Validate and save an uploaded banner file.
     *
     * @param UploadedFile $file
     * @param string $attributeKey
     * @return string Full public URL
     * @throws ValidationException
     */
    public function saveUploadedBanner(UploadedFile $file, string $attributeKey = 'img_file'): string
    {
        if (!$file->isValid()) {
            throw ValidationException::withMessages([
                $attributeKey => [__('messages.invalid_image_file') ?: 'Uploaded file is not valid.'],
            ]);
        }

        // Size check (5MB)
        $sizeKb = $file->getSize() / 1024;
        if ($sizeKb > self::MAX_FILE_SIZE_KB) {
            throw ValidationException::withMessages([
                $attributeKey => [__('messages.file_too_large') ?: 'The image must not be greater than 5MB.'],
            ]);
        }

        // Extension & MIME check
        $extension = strtolower((string) ($file->guessExtension() ?: $file->getClientOriginalExtension() ?: 'png'));
        if ($extension === 'jpeg') {
            $extension = 'jpg';
        }

        if (!in_array($extension, self::ALLOWED_EXTENSIONS, true)) {
            throw ValidationException::withMessages([
                $attributeKey => [__('messages.invalid_image_format') ?: 'Allowed image types: JPG, PNG, GIF, WEBP, SVG.'],
            ]);
        }

        // MIME type inspection for extra security
        $mime = $file->getMimeType();
        if ($mime && !str_starts_with($mime, 'image/') && $extension !== 'svg') {
            throw ValidationException::withMessages([
                $attributeKey => [__('messages.invalid_image_file') ?: 'The file must be a valid image.'],
            ]);
        }

        $targetDir = base_path(self::UPLOAD_SUBDIR);
        if (!File::isDirectory($targetDir)) {
            File::makeDirectory($targetDir, 0755, true);
        }

        $filename = 'banner_' . time() . '_' . Str::random(12) . '.' . $extension;
        $file->move($targetDir, $filename);

        return url(self::UPLOAD_SUBDIR . '/' . $filename);
    }

    /**
     * Normalize URL string (add https:// if missing scheme and not a relative or data URI).
     */
    public function normalizeImageUrl(string $url): string
    {
        $url = trim($url);
        if ($url === '') {
            return '';
        }

        // If it starts with data: or relative path /
        if (str_starts_with($url, 'data:') || str_starts_with($url, '/')) {
            return $url;
        }

        if (!preg_match('~^https?://~i', $url)) {
            return 'https://' . $url;
        }

        return $url;
    }

    /**
     * Check if a URL is an internal or uploaded banner URL.
     */
    public function isInternalUrl(?string $url): bool
    {
        if ($url === null) {
            return false;
        }

        $trimmed = trim($url);
        if ($trimmed === '') {
            return false;
        }

        if (str_starts_with($trimmed, 'upload/') || str_starts_with($trimmed, '/upload/')) {
            return true;
        }

        $host = parse_url($trimmed, PHP_URL_HOST);
        if ($host) {
            $currentHost = request()->getHost();
            if ($host === $currentHost || $host === 'localhost' || $host === '127.0.0.1') {
                $path = parse_url($trimmed, PHP_URL_PATH) ?? '';
                if (str_contains($path, 'upload/banners') || str_contains($path, 'upload/')) {
                    return true;
                }
            }
        }

        return false;
    }
}
