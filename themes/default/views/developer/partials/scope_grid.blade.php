@php
    $selectedScopes = $selectedScopes ?? [];
    if (!is_array($selectedScopes)) {
        $selectedScopes = [];
    }

    $scopeInputPrefix = $scopeInputPrefix ?? 'developer_scope';

    $developerScopeFallbacks = [
        'user.identity.read' => [
            'label' => 'Identity',
            'description' => 'Read the member account identifier and basic public identity fields.',
        ],
        'user.profile.read' => [
            'label' => 'Profile',
            'description' => 'Read public profile details and core member metadata.',
        ],
        'user.email.read' => [
            'label' => 'Email Address',
            'description' => 'Access the primary verified email address of the member.',
        ],
        'user.social_links.read' => [
            'label' => 'Social Links',
            'description' => 'Read the public social links configured on a member profile.',
        ],
        'user.follows.read' => [
            'label' => 'Follow Graph',
            'description' => 'Read follower and following relationships for visible members.',
        ],
        'user.follows.write' => [
            'label' => 'Manage Follows',
            'description' => 'Follow or unfollow other members on behalf of the user.',
        ],
        'user.content.read' => [
            'label' => 'Read Content & Posts',
            'description' => 'Read public posts and status updates authored by the user.',
        ],
        'user.content.write' => [
            'label' => 'Publish Posts',
            'description' => 'Create, update, and publish posts on behalf of the user.',
        ],
        'user.reactions.write' => [
            'label' => 'React to Content',
            'description' => 'Add or toggle likes and reactions to content on behalf of the user.',
        ],
        'user.comments.write' => [
            'label' => 'Post Comments',
            'description' => 'Publish comments and replies on posts on behalf of the user.',
        ],
        'user.messages.read' => [
            'label' => 'Read Private Messages',
            'description' => 'Read private direct message conversations belonging to the user.',
        ],
        'user.messages.write' => [
            'label' => 'Send Private Messages',
            'description' => 'Send private direct messages on behalf of the user.',
        ],
        'user.notifications.read' => [
            'label' => 'Read Notifications',
            'description' => 'Read account notifications, alerts, and unread counters.',
        ],
        'user.wallet.read' => [
            'label' => 'Read Wallet & Balance',
            'description' => 'Read user points balance, rewards, and wallet details.',
        ],
        'user.badges.read' => [
            'label' => 'Read Badges & Achievements',
            'description' => 'Read member badges, unlocked achievements, and quest status.',
        ],
        'user.clips.read' => [
            'label' => 'Read Video Clips',
            'description' => 'Browse short video clips feed and saved clips in user account.',
        ],
        'user.clips.write' => [
            'label' => 'Manage Saved Clips',
            'description' => 'Save and unsave short video clips on behalf of the user.',
        ],
        'user.forums.read' => [
            'label' => 'Read Forums',
            'description' => 'Read forum categories, topics, discussions, and replies.',
        ],
        'user.forums.write' => [
            'label' => 'Participate in Forums',
            'description' => 'Create new topics and post replies in forums on behalf of the user.',
        ],
        'user.store.read' => [
            'label' => 'Browse Store',
            'description' => 'Browse marketplace products, offerings, and store knowledgebase.',
        ],
        'user.orders.read' => [
            'label' => 'Read Orders & Purchases',
            'description' => 'Read user purchase orders history and submitted offers.',
        ],
        'user.ads.read' => [
            'label' => 'Read Ads Statistics',
            'description' => 'Read ad impression counts, clicks, and campaign performance statistics.',
        ],
        'owner.profile.read' => [
            'label' => 'Owner Profile',
            'description' => 'Read the authenticated owner profile through the developer API.',
        ],
        'owner.content.read' => [
            'label' => 'Owner Content',
            'description' => 'Read the authenticated owner content feed and published updates.',
        ],
        'owner.follow.write' => [
            'label' => 'Owner Follow Write',
            'description' => 'Follow or unfollow members on behalf of the authorized owner.',
        ],
        'owner.messages.read' => [
            'label' => 'Owner Messages Read',
            'description' => 'Read private message conversations that belong to the authorized owner.',
        ],
        'owner.messages.write' => [
            'label' => 'Owner Messages Write',
            'description' => 'Send private messages on behalf of the authorized owner.',
        ],
    ];

    $categories = \App\Services\DeveloperScopeCatalog::getCategories();
    
    // Group scopes by category
    $groupedScopes = [];
    $uncategorizedScopes = [];
    foreach ($scopes as $scopeId => $scope) {
        $cat = $scope['category'] ?? null;
        if ($cat && isset($categories[$cat])) {
            $groupedScopes[$cat][$scopeId] = $scope;
        } else {
            $uncategorizedScopes[$scopeId] = $scope;
        }
    }
@endphp

<div class="dev-scopes-container" style="display: flex; flex-direction: column; gap: 24px;">
    @foreach($categories as $catKey => $catMeta)
        @if(!empty($groupedScopes[$catKey]))
            <div class="dev-scope-category">
                <div class="dev-scope-category-head">
                    <i class="fa {{ $catMeta['icon'] ?? 'fa-layer-group' }}"></i>
                    <h4>{{ __($catMeta['title']) }}</h4>
                </div>
                <div class="dev-scope-grid">
                    @foreach($groupedScopes[$catKey] as $scopeId => $scope)
                        @php
                            $translatedLabel = __($scope['name']);
                            $translatedDescription = __($scope['description']);
                            $scopeFallback = $developerScopeFallbacks[$scopeId] ?? null;
                            $scopeLabel = $translatedLabel === $scope['name']
                                ? ($scopeFallback['label'] ?? ucwords(str_replace('.', ' ', $scopeId)))
                                : $translatedLabel;
                            $scopeDescription = $translatedDescription === $scope['description']
                                ? ($scopeFallback['description'] ?? $scopeId)
                                : $translatedDescription;
                        @endphp

                        <div class="dev-scope-card">
                            <div class="dev-scope-copy">
                                <div class="dev-scope-header">
                                    <input
                                        class="dev-check-input"
                                        type="checkbox"
                                        name="requested_scopes[]"
                                        value="{{ $scopeId }}"
                                        id="{{ $scopeInputPrefix }}_{{ str_replace('.', '_', $scopeId) }}"
                                        @checked(in_array($scopeId, $selectedScopes, true))
                                    >
                                    <label class="dev-scope-label" for="{{ $scopeInputPrefix }}_{{ str_replace('.', '_', $scopeId) }}">
                                        <span>{{ $scopeLabel }}</span>
                                        @if(!empty($scope['is_sensitive']))
                                            <span class="badge bg-danger">{{ __('messages.sensitive') }}</span>
                                        @endif
                                    </label>
                                </div>
                                <div class="dev-scope-description">{{ $scopeDescription }}</div>
                                <code>{{ $scopeId }}</code>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach

    @if(!empty($uncategorizedScopes))
        <div class="dev-scope-category">
            <div class="dev-scope-category-head">
                <i class="fa fa-shield"></i>
                <h4>{{ __('messages.other') }}</h4>
            </div>
            <div class="dev-scope-grid">
                @foreach($uncategorizedScopes as $scopeId => $scope)
                    @php
                        $translatedLabel = __($scope['name']);
                        $translatedDescription = __($scope['description']);
                        $scopeFallback = $developerScopeFallbacks[$scopeId] ?? null;
                        $scopeLabel = $translatedLabel === $scope['name']
                            ? ($scopeFallback['label'] ?? ucwords(str_replace('.', ' ', $scopeId)))
                            : $translatedLabel;
                        $scopeDescription = $translatedDescription === $scope['description']
                            ? ($scopeFallback['description'] ?? $scopeId)
                            : $translatedDescription;
                    @endphp

                    <div class="dev-scope-card">
                        <div class="dev-scope-copy">
                            <div class="dev-scope-header">
                                <input
                                    class="dev-check-input"
                                    type="checkbox"
                                    name="requested_scopes[]"
                                    value="{{ $scopeId }}"
                                    id="{{ $scopeInputPrefix }}_{{ str_replace('.', '_', $scopeId) }}"
                                    @checked(in_array($scopeId, $selectedScopes, true))
                                >
                                <label class="dev-scope-label" for="{{ $scopeInputPrefix }}_{{ str_replace('.', '_', $scopeId) }}">
                                    <span>{{ $scopeLabel }}</span>
                                    @if(!empty($scope['is_sensitive']))
                                        <span class="badge bg-danger">{{ __('messages.sensitive') }}</span>
                                    @endif
                                </label>
                            </div>
                            <div class="dev-scope-description">{{ $scopeDescription }}</div>
                            <code>{{ $scopeId }}</code>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

