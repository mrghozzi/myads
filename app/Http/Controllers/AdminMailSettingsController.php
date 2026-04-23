<?php

namespace App\Http\Controllers;

use App\Models\MailSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class AdminMailSettingsController extends Controller
{
    /**
     * Display the mail settings form.
     */
    public function index()
    {
        $settings = MailSetting::current();

        return view('admin::admin.mail_settings', compact('settings'));
    }

    /**
     * Validate and persist mail settings to the database.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'mail_mailer'       => 'required|in:smtp,sendmail,log,array',
            'mail_host'         => 'required_if:mail_mailer,smtp|nullable|string|max:255',
            'mail_port'         => 'required_if:mail_mailer,smtp|nullable|integer|min:1|max:65535',
            'mail_username'     => 'nullable|string|max:255',
            'mail_password'     => 'nullable|string|max:1000',
            'mail_encryption'   => 'nullable|in:tls,ssl',
            'mail_from_address' => 'required|email|max:255',
            'mail_from_name'    => 'required|string|max:255',
        ]);

        // Normalize empty encryption to null (not the string "null")
        $validated['mail_encryption'] = $validated['mail_encryption'] ?: null;

        // Load or create the single settings row
        $settings = MailSetting::query()->first();

        if ($settings) {
            // If the password field was left blank, keep the existing encrypted value
            if (empty($validated['mail_password'])) {
                unset($validated['mail_password']);
            }

            $settings->update($validated);
        } else {
            MailSetting::create($validated);
        }

        // Clear config cache so the provider picks up changes on next request
        try {
            Artisan::call('config:clear');
        } catch (\Exception $e) {
            // May fail on some shared hosts — non-critical
        }

        return redirect()
            ->route('admin.settings.mail')
            ->with('success', __('messages.mail_settings_saved'));
    }
}
