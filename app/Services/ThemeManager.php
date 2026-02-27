<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use App\Models\Setting;
use Illuminate\Support\Facades\Log;

class ThemeManager
{
    protected $themePath;

    public function __construct()
    {
        $this->themePath = base_path('themes');
    }

    /**
     * Get all themes.
     *
     * @return array
     */
    public function getAllThemes()
    {
        $themes = [];
        if (!File::exists($this->themePath)) {
            File::makeDirectory($this->themePath, 0755, true);
        }

        $directories = File::directories($this->themePath);
        $activeThemeSlug = $this->getActiveThemeSlug();

        foreach ($directories as $directory) {
            $jsonFile = $directory . '/theme.json';
            
            if (File::exists($jsonFile)) {
                $themeData = json_decode(File::get($jsonFile), true);
                
                if ($themeData) {
                    $themeData['directory'] = basename($directory);
                    $themeData['path'] = $directory;
                    
                    // Add preview image path if exists
                    $screenshotPath = $directory . '/screenshot.png';
                    $themeData['screenshot'] = File::exists($screenshotPath) 
                        ? asset('themes/' . basename($directory) . '/screenshot.png') 
                        : null;

                    $themeData['is_active'] = ($themeData['slug'] === $activeThemeSlug);
                    
                    $themes[] = $themeData;
                }
            }
        }

        return $themes;
    }

    /**
     * Get the active theme slug.
     *
     * @return string|null
     */
    public function getActiveThemeSlug()
    {
        $setting = Setting::first();
        return $setting ? $setting->styles : 'default';
    }

    /**
     * Activate a theme.
     *
     * @param string $slug
     * @return bool
     */
    public function activate($slug)
    {
        // Find theme directory by slug
        $themeDir = $this->findDirectoryBySlug($slug);
        
        if (!$themeDir) {
            return false;
        }

        $setting = Setting::first();
        if ($setting) {
            $setting->update(['styles' => $slug]);
            return true;
        }

        return false;
    }

    /**
     * Find theme directory name by slug.
     *
     * @param string $slug
     * @return string|null
     */
    protected function findDirectoryBySlug($slug)
    {
        $directories = File::directories($this->themePath);
        
        foreach ($directories as $directory) {
            $jsonFile = $directory . '/theme.json';
            if (File::exists($jsonFile)) {
                $data = json_decode(File::get($jsonFile), true);
                if ($data && isset($data['slug']) && $data['slug'] === $slug) {
                    return basename($directory);
                }
            }
        }
        
        return null;
    }
}
