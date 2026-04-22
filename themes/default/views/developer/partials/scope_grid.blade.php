@php
    $selectedScopes = $selectedScopes ?? [];
    if (!is_array($selectedScopes)) {
        $selectedScopes = [];
    }

    $developerScopeFallbacks = [
        'user.identity.read' => [
            'label' => 'Identity',
            'description' => 'Read the member account identifier and basic public identity fields.',
        ],
        'user.profile.read' => [
            'label' => 'Profile',
            'description' => 'Read public profile details and core member metadata.',
        ],
        'user.social_links.read' => [
            'label' => 'Social Links',
            'description' => 'Read the public social links configured on a member profile.',
        ],
        'user.follows.read' => [
            'label' => 'Follow Graph',
            'description' => 'Read follower and following relationships for visible members.',
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

    $scopeInputPrefix = $scopeInputPrefix ?? 'developer_scope';
@endphp

<div class="dev-scope-grid">
    @foreach($scopes as $scopeId => $scope)
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
            <input
                class="form-check-input"
                type="checkbox"
                name="requested_scopes[]"
                value="{{ $scopeId }}"
                id="{{ $scopeInputPrefix }}_{{ str_replace('.', '_', $scopeId) }}"
                @checked(in_array($scopeId, $selectedScopes, true))
            >
            <div class="dev-scope-copy">
                <label class="dev-scope-label" for="{{ $scopeInputPrefix }}_{{ str_replace('.', '_', $scopeId) }}">
                    <span>{{ $scopeLabel }}</span>
                    @if(!empty($scope['is_sensitive']))
                        <span class="badge bg-danger">{{ __('messages.sensitive') }}</span>
                    @endif
                </label>
                <div>{{ $scopeDescription }}</div>
                <code>{{ $scopeId }}</code>
            </div>
        </div>
    @endforeach
</div>
