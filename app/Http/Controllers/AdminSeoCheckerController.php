<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Option;

class AdminSeoCheckerController extends Controller
{
    /**
     * Show the SEO Checker settings page.
     */
    public function settings()
    {
        // Get existing settings
        $option = Option::where('name', 'seo_checker_settings')->first();
        $settings = $option ? json_decode($option->o_valuer, true) : $this->getDefaultSettings();

        return view('admin::seo_checker.settings', compact('settings'));
    }

    /**
     * Update the SEO Checker settings.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'speed' => 'required|in:guest,member,premium',
            'errors' => 'required|in:guest,member,premium',
            'backlinks' => 'required|in:guest,member,premium',
        ]);

        Option::updateOrCreate(
            ['name' => 'seo_checker_settings'],
            [
                'o_type' => 'settings',
                'o_valuer' => json_encode($validated)
            ]
        );

        return redirect()->route('admin.seo_checker.settings')->with('success', __('Settings updated successfully.'));
    }

    /**
     * Default settings for the SEO checker
     */
    private function getDefaultSettings(): array
    {
        return [
            'speed' => 'guest',
            'errors' => 'member',
            'backlinks' => 'premium',
        ];
    }
}
