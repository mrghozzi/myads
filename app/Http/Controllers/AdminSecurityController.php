<?php

namespace App\Http\Controllers;

use App\Http\Middleware\RequireAdminPasswordConfirmation;
use App\Models\SecurityIpBan;
use App\Models\SecurityMemberSession;
use App\Services\SecuritySessionService;
use App\Services\V420SchemaService;
use App\Support\SecuritySettings;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminSecurityController extends Controller
{
    public function __construct(
        private readonly V420SchemaService $schema,
        private readonly SecuritySessionService $sessionService
    ) {
    }

    public function index()
    {
        $settings = SecuritySettings::all();
        $ipBansAvailable = $this->schema->supports('security_ip_bans');
        $sessionsAvailable = $this->schema->supports('security_sessions');
        $upgradeNotices = array_values(array_filter([
            $this->schema->notice('security_ip_bans', __('messages.security_ip_bans_title')),
            $this->schema->notice('security_sessions', __('messages.security_member_sessions_title')),
        ]));

        return view('admin::admin.security.index', compact(
            'settings',
            'ipBansAvailable',
            'sessionsAvailable',
            'upgradeNotices'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'blacklist_domains' => ['nullable', 'string'],
            'blacklist_url_patterns' => ['nullable', 'string'],
            'blocked_usernames' => ['nullable', 'string'],
            'blocked_email_domains' => ['nullable', 'string'],
            'cooldown_post_seconds' => ['nullable', 'integer', 'min:0'],
            'cooldown_comment_seconds' => ['nullable', 'integer', 'min:0'],
            'cooldown_forum_topic_seconds' => ['nullable', 'integer', 'min:0'],
            'cooldown_private_message_seconds' => ['nullable', 'integer', 'min:0'],
            'registration_ip_daily_limit' => ['nullable', 'integer', 'min:0'],
            'admin_password_confirmation_ttl_minutes' => ['nullable', 'integer', 'min:0'],
            'login_max_attempts_per_ip_15m' => ['nullable', 'integer', 'min:0'],
            'login_max_attempts_per_account_15m' => ['nullable', 'integer', 'min:0'],
            'max_active_sessions_per_user' => ['nullable', 'integer', 'min:0'],
        ]);

        SecuritySettings::save($request->all());

        return redirect()->route('admin.security.index')
            ->with('success', __('messages.security_settings_saved'));
    }

    public function ipBans(Request $request)
    {
        $featureAvailable = $this->schema->supports('security_ip_bans');
        $upgradeNotice = $this->schema->notice('security_ip_bans', __('messages.security_ip_bans_title'));
        $status = (string) $request->query('status', 'active');
        $search = trim((string) $request->query('q', ''));

        $bans = $featureAvailable
            ? SecurityIpBan::query()
                ->with('bannedBy:id,username')
                ->when($status === 'active', function ($query) {
                    $query->where('is_active', true)
                        ->where(function ($builder) {
                            $builder->whereNull('expires_at')
                                ->orWhere('expires_at', '>', now());
                        });
                })
                ->when($status === 'expired', function ($query) {
                    $query->whereNotNull('expires_at')
                        ->where('expires_at', '<=', now());
                })
                ->when($status === 'inactive', fn ($query) => $query->where('is_active', false))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder->where('ip_address', 'like', '%' . $search . '%')
                            ->orWhere('reason', 'like', '%' . $search . '%');
                    });
                })
                ->latest('id')
                ->paginate(20)
            : new LengthAwarePaginator([], 0, 20, $request->integer('page', 1), [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

        return view('admin::admin.security.ip_bans', compact(
            'bans',
            'featureAvailable',
            'upgradeNotice',
            'status',
            'search'
        ));
    }

    public function storeIpBan(Request $request)
    {
        if (!$this->schema->supports('security_ip_bans')) {
            return redirect()->route('admin.security.ip-bans')
                ->with('error', $this->schema->blockedActionMessage('security_ip_bans', __('messages.security_ip_bans_title')));
        }

        $validated = $request->validate([
            'ip_address' => ['required', 'ip'],
            'reason' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        SecurityIpBan::query()->updateOrCreate(
            ['ip_address' => $validated['ip_address']],
            [
                'reason' => trim((string) ($validated['reason'] ?? '')) ?: null,
                'expires_at' => $validated['expires_at'] ?? null,
                'is_active' => $request->boolean('is_active', true),
                'banned_by' => Auth::id(),
            ]
        );

        return redirect()->route('admin.security.ip-bans')
            ->with('success', __('messages.security_ip_ban_saved'));
    }

    public function destroyIpBan(int $id)
    {
        if (!$this->schema->supports('security_ip_bans')) {
            return redirect()->route('admin.security.ip-bans')
                ->with('error', $this->schema->blockedActionMessage('security_ip_bans', __('messages.security_ip_bans_title')));
        }

        $ban = SecurityIpBan::query()->findOrFail($id);
        $ban->delete();

        return redirect()->route('admin.security.ip-bans')
            ->with('success', __('messages.security_ip_ban_deleted'));
    }

    public function sessions(Request $request)
    {
        $featureAvailable = $this->schema->supports('security_sessions');
        $upgradeNotice = $this->schema->notice('security_sessions', __('messages.security_member_sessions_title'));
        $status = (string) $request->query('status', 'active');
        $search = trim((string) $request->query('q', ''));

        $sessions = $featureAvailable
            ? SecurityMemberSession::query()
                ->with(['user:id,username,email', 'revokedBy:id,username'])
                ->when($status === 'active', fn ($query) => $query->active())
                ->when($status === 'ended', fn ($query) => $query->whereNotNull('ended_at')->whereNull('revoked_at'))
                ->when($status === 'revoked', fn ($query) => $query->whereNotNull('revoked_at'))
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($builder) use ($search) {
                        $builder->where('session_id', 'like', '%' . $search . '%')
                            ->orWhere('ip_address', 'like', '%' . $search . '%')
                            ->orWhere('user_agent', 'like', '%' . $search . '%')
                            ->orWhereHas('user', function ($userQuery) use ($search) {
                                $userQuery->where('username', 'like', '%' . $search . '%')
                                    ->orWhere('email', 'like', '%' . $search . '%');
                            });
                    });
                })
                ->orderByDesc('last_seen_at')
                ->orderByDesc('started_at')
                ->paginate(25)
            : new LengthAwarePaginator([], 0, 25, $request->integer('page', 1), [
                'path' => $request->url(),
                'query' => $request->query(),
            ]);

        return view('admin::admin.security.sessions', compact(
            'sessions',
            'featureAvailable',
            'upgradeNotice',
            'status',
            'search'
        ));
    }

    public function revokeSession(Request $request, int $id)
    {
        if (!$this->schema->supports('security_sessions')) {
            return redirect()->route('admin.security.sessions')
                ->with('error', $this->schema->blockedActionMessage('security_sessions', __('messages.security_member_sessions_title')));
        }

        $session = SecurityMemberSession::query()->findOrFail($id);
        $this->sessionService->revoke($session, $request->user());

        if ((string) $session->session_id === (string) $request->session()->getId()) {
            $this->sessionService->markLogout($request, $request->user());
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')
                ->with('success', __('messages.security_session_revoked_current'));
        }

        return redirect()->route('admin.security.sessions')
            ->with('success', __('messages.security_session_revoked_success'));
    }

    public function showConfirmPasswordForm()
    {
        return view('admin::admin.security.confirm_password');
    }

    public function confirmPassword(Request $request)
    {
        $request->validate([
            'password' => ['required', 'string'],
        ]);

        $user = $request->user();
        if (!$user || !Hash::check($request->input('password'), $user->getAuthPassword())) {
            return back()->withErrors([
                'password' => __('messages.security_admin_password_invalid'),
            ]);
        }

        RequireAdminPasswordConfirmation::markConfirmed($request);

        return redirect()->intended(route('admin.index'))
            ->with('success', __('messages.security_admin_password_confirmed'));
    }
}
