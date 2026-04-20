@php
    $groupBadge = $groupBadge
        ?? ($group ?? null)
        ?? ($activity->group ?? null)
        ?? ($topic->group ?? null)
        ?? null;
@endphp

@if($groupBadge)
    @once
        @push('head')
            <style>
                .group-context-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    padding: 8px 12px;
                    border-radius: 999px;
                    background: rgba(35, 210, 226, 0.12);
                    color: #2f3142;
                    font-size: 0.78rem;
                    font-weight: 700;
                    line-height: 1;
                    text-decoration: none;
                }

                .group-context-badge:hover {
                    text-decoration: none;
                    color: #615dfa;
                    transform: translateY(-1px);
                }

                .group-context-badge__meta {
                    color: #8f91ac;
                    font-size: 0.72rem;
                    font-weight: 600;
                }

                body[data-theme="css_d"] .group-context-badge {
                    background: rgba(79, 244, 97, 0.12);
                    color: #f5f7ff;
                }

                body[data-theme="css_d"] .group-context-badge__meta {
                    color: #9aa4bf;
                }
            </style>
        @endpush
    @endonce

    <a class="group-context-badge" href="{{ route('groups.show', $groupBadge) }}">
        <i class="fa fa-users" aria-hidden="true"></i>
        <span>{{ $groupBadge->name }}</span>
        <span class="group-context-badge__meta">
            {{ $groupBadge->privacy === \App\Models\Group::PRIVACY_PUBLIC ? __('messages.groups_public') : __('messages.groups_private') }}
        </span>
    </a>
@endif
