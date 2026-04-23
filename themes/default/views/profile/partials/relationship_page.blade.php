@include('theme::profile._relationships_assets')

@php
    $relationshipTotal = method_exists($relationshipItems, 'total')
        ? (int) $relationshipItems->total()
        : (int) $relationshipItems->count();
@endphp

@include('theme::profile.partials.relationship_header', ['selectedTab' => $selectedTab])
@include('theme::profile.navigation', ['selectedTab' => $selectedTab])

<div class="grid grid-3-6-3 mobile-prefer-content">
    <div class="grid-column">
        @include('theme::profile.partials.relationship_summary_card', [
            'relationshipTotal' => $relationshipTotal,
        ])

        <x-widget-column side="profile_left" />
    </div>

    <div class="grid-column">
        <div class="section-header profile-relationships-section-header">
            <div class="section-header-info">
                <p class="section-pretitle">{{ $user->username }}</p>
                <h2 class="section-title">{{ $relationshipTitle }}</h2>
                <p class="profile-relationships-section-caption">{{ number_format($relationshipTotal) }}</p>
            </div>

            <div class="profile-relationships-count">
                <p class="profile-relationships-count__value">{{ number_format($relationshipTotal) }}</p>
                <p class="profile-relationships-count__label">{{ $relationshipTitle }}</p>
            </div>
        </div>

        <div class="profile-relationships-list">
            @forelse($relationshipItems as $relationship)
                @php
                    $targetUser = $relationshipType === 'followers'
                        ? $relationship->user
                        : $relationship->targetUser;
                @endphp

                @if($targetUser)
                    @include('theme::profile.partials.relationship_member_card', [
                        'targetUser' => $targetUser,
                        'actionTime' => $relationship->date ?? $relationship->time_t,
                        'isViewerFollowingTarget' => in_array((int) $targetUser->id, $viewerFollowingIds ?? [], true),
                    ])
                @endif
            @empty
                <div class="widget-box profile-relationships-empty">
                    <div class="profile-empty-state">
                        <div class="profile-relationships-empty__icon">
                            <i class="fa fa-users" aria-hidden="true"></i>
                        </div>
                        <p class="widget-box-title">{{ $emptyMessage }}</p>
                    </div>
                </div>
            @endforelse
        </div>

        @if(method_exists($relationshipItems, 'hasPages') && $relationshipItems->hasPages())
            <div class="profile-relationships-pagination">
                {{ $relationshipItems->links() }}
            </div>
        @endif
    </div>

    <div class="grid-column">
        @include('theme::partials.widgets', ['place' => 8])
    </div>
</div>

@once
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                if (typeof initHexagons === 'function') {
                    initHexagons();
                }
            });
        </script>
    @endpush
@endonce
