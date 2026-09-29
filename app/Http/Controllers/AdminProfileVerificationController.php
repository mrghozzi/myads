<?php

namespace App\Http\Controllers;

use App\Models\ProfileVerificationRequest;
use App\Services\ProfileVerificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminProfileVerificationController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureUserManagementAccess();

        $status = $request->query('status', 'pending');
        abort_unless(in_array($status, ['pending', 'approved', 'rejected', 'all'], true), 404);

        $query = ProfileVerificationRequest::with(['user', 'reviewer'])->latest();
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->whereHas('user', function ($userQuery) use ($search): void {
                $userQuery->where('username', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $requests = $query->paginate(20)->withQueryString();
        $selectedRequest = $request->integer('request')
            ? ProfileVerificationRequest::with(['user', 'reviewer'])->find($request->integer('request'))
            : $requests->first();
        $stats = [
            'pending' => ProfileVerificationRequest::where('status', 'pending')->count(),
            'approved' => ProfileVerificationRequest::where('status', 'approved')->count(),
            'rejected' => ProfileVerificationRequest::where('status', 'rejected')->count(),
        ];

        return view('admin::admin.profile_verification.requests', compact('requests', 'selectedRequest', 'stats', 'status', 'search'));
    }

    public function review(Request $request, ProfileVerificationRequest $verificationRequest)
    {
        $this->ensureUserManagementAccess();
        $validated = $request->validate([
            'decision' => ['required', 'in:approved,rejected'],
            'reviewer_note' => ['nullable', 'string', 'max:3000'],
        ]);

        DB::transaction(function () use ($request, $verificationRequest, $validated): void {
            $lockedRequest = ProfileVerificationRequest::whereKey($verificationRequest->id)->lockForUpdate()->firstOrFail();
            abort_unless($lockedRequest->status === 'pending', 422, __('messages.verification_request_already_reviewed'));

            if ($validated['decision'] === 'approved') {
                $lockedRequest->user()->update(['ucheck' => 1]);
            }

            $lockedRequest->update([
                'status' => $validated['decision'],
                'reviewer_note' => $validated['reviewer_note'] ?? null,
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
        });

        return redirect()->route('admin.profile_verification.requests', ['status' => 'pending'])
            ->with('success', __('messages.verification_review_saved'));
    }

    public function settings(ProfileVerificationService $verification)
    {
        $this->ensureUserManagementAccess();
        $settings = $verification->settings();

        return view('admin::admin.profile_verification.settings', compact('settings'));
    }

    public function updateSettings(Request $request, ProfileVerificationService $verification)
    {
        $this->ensureUserManagementAccess();
        $validated = $request->validate([
            'min_account_age_days' => ['required', 'integer', 'min:0', 'max:36500'],
            'min_followers_count' => ['required', 'integer', 'min:0', 'max:100000000'],
        ]);

        $verification->set('enabled', $request->boolean('enabled') ? '1' : '0');
        $verification->set('require_verified_email', $request->boolean('require_verified_email') ? '1' : '0');
        $verification->set('min_account_age_days', $validated['min_account_age_days']);
        $verification->set('min_followers_count', $validated['min_followers_count']);

        return back()->with('success', __('messages.verification_settings_saved'));
    }

    public function terms(ProfileVerificationService $verification)
    {
        $this->ensureUserManagementAccess();
        $locale = request()->query('locale', app()->getLocale());
        abort_unless(in_array($locale, $verification->supportedLocales(), true), 404);
        $settings = $verification->settings($locale);
        $locales = $verification->supportedLocales();

        return view('admin::admin.profile_verification.terms', compact('settings', 'locales', 'locale'));
    }

    public function updateTerms(Request $request, ProfileVerificationService $verification)
    {
        $this->ensureUserManagementAccess();
        $validated = $request->validate([
            'locale' => ['required', 'string', 'in:' . implode(',', $verification->supportedLocales())],
            'terms' => ['required', 'string', 'min:10', 'max:20000'],
        ]);
        $verification->set('terms_' . $validated['locale'], $validated['terms']);

        return redirect()->route('admin.profile_verification.terms', ['locale' => $validated['locale']])
            ->with('success', __('messages.verification_terms_saved'));
    }

    private function ensureUserManagementAccess(): void
    {
        abort_unless(auth()->user()?->canAccessAdminModule('users'), 403);
    }
}
