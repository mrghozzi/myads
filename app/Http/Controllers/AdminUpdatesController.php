<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\Option;
use ZipArchive;

class AdminUpdatesController extends Controller
{
    private $myads_last_time_updates = "https://github.com/mrghozzi/myads_check_updates/raw/main/latest_version.txt";
    private $myads_last_updates = "https://github.com/mrghozzi/myads_check_updates/raw/main/last_updates.txt";

    public function index(Request $request)
    {
        // Define version logic similar to old myads_version.php
        $myads_generation = "4";
        $myads_Version = "0";
        $myads_Update = "0";
        $stversion = "{$myads_generation}.{$myads_Version}.{$myads_Update}";
        $version_name = "{$myads_generation}-{$myads_Version}-{$myads_Update}";

        // Check for current version in DB
        $currentVersionOption = Option::where('name', 'version')->first();

        // If not in DB, insert it (Migration logic from old script)
        if (!$currentVersionOption) {
            $currentVersionOption = Option::create([
                'name' => 'version', // Was $version_name in old script logic but mapped to name column
                'o_valuer' => $stversion,
                'o_type' => 'version',
                'o_parent' => 0,
                'o_order' => 0,
                'o_mode' => '0'
            ]);
        } else {
             // If version in DB is different from hardcoded, update DB? 
             // The old script logic: if($versionRow['o_valuer']==$stversion){}else{ UPDATE }
             // This ensures DB reflects the code version.
             if ($currentVersionOption->o_valuer != $stversion) {
                 $currentVersionOption->update([
                     'o_valuer' => $stversion,
                     // 'name' => $version_name // Do we want to update name? Old script did.
                 ]);
             }
        }
        
        $currentVersion = $currentVersionOption->o_valuer;

        // Fetch latest version
        $latestVersion = Cache::remember('latest_version', 3600, function () {
            try {
                $response = Http::timeout(5)->get('https://github.com/mrghozzi/myads_check_updates/raw/main/latest_version.txt');
                if ($response->successful()) {
                    return trim(strip_tags($response->body()));
                }
            } catch (\Exception $e) {
                return null;
            }
            return null;
        });

        return view('theme::admin.updates', compact('currentVersion', 'latestVersion'));
    }

    public function update(Request $request)
    {
        // 1. Download Zip
        try {
            // Get the URL from the text file
            $response = Http::timeout(10)->get('https://github.com/mrghozzi/myads_check_updates/raw/main/last_updates.txt');
            
            if (!$response->successful()) {
                return redirect()->back()->with('error', 'Could not fetch update URL.');
            }
            
            $downloadUrl = trim($response->body());
            
            // Download the zip file
            $zipContent = Http::timeout(60)->get($downloadUrl)->body();
            $tempZipPath = storage_path('app/temp_update.zip');
            File::put($tempZipPath, $zipContent);

            // 2. Extract
            $zip = new \ZipArchive;
            if ($zip->open($tempZipPath) === TRUE) {
                $extractPath = base_path(); // Extract to root
                $zip->extractTo($extractPath);
                $zip->close();
                
                // 3. Run Update Script if exists (requests/update.php in old)
                $updateScript = base_path('requests/update.php');
                if (File::exists($updateScript)) {
                    include_once $updateScript;
                }
                
                // Also run migrations
                \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
                \Illuminate\Support\Facades\Artisan::call('cache:clear');
                \Illuminate\Support\Facades\Artisan::call('view:clear');

                File::delete($tempZipPath);
                
                return redirect()->route('admin.updates')->with('success', 'System updated successfully!');

            } else {
                return redirect()->route('admin.updates')->with('error', 'Failed to open update zip file.');
            }

        } catch (\Exception $e) {
             return redirect()->route('admin.updates')->with('error', 'Update failed: ' . $e->getMessage());
        }
    }
}
