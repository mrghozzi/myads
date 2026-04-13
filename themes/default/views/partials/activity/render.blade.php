@php
    $type = 'text'; // Default
    if ($activity->s_type == 4) {
        $type = 'image';
    } elseif ($activity->s_type == 2) {
        $type = 'topic';
    } elseif ($activity->s_type == 1) {
        $type = 'site';
    } elseif ($activity->s_type == 7867) {
        $type = 'store';
    } elseif ($activity->s_type == 205) {
        $type = 'knowledgebase';
    } elseif ($activity->s_type == 5 || $activity->s_type == 'news') { 
        $type = 'news';
    } elseif ($activity->s_type == 6) {
        $type = 'order';
    }
    
    // Check if related content exists before including
    if (!$activity->related_content) {
        // If related content is missing, we might want to skip or show error.
        // For now, we'll try to render, but the partials assume related_content exists.
        // We can check inside partials or here.
        // Given the wrapper checked it, we assume it's checked.
    }
@endphp

@if($activity->related_content)
    @include('theme::partials.activity.types.' . $type, ['activity' => $activity, 'detailView' => $detailView ?? false])
@endif

@once
    @push('scripts')
        <script>
            if (typeof window.openRepostComposer !== 'function') {
                window.openRepostComposer = function (statusId, authorName, excerpt) {
                    const quote = window.prompt('{{ __('messages.quote_repost_prompt') }}', '');
                    if (quote === null) {
                        return;
                    }

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route('status.create') }}';
                    form.style.display = 'none';

                    const token = document.createElement('input');
                    token.type = 'hidden';
                    token.name = '_token';
                    token.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

                    const kind = document.createElement('input');
                    kind.type = 'hidden';
                    kind.name = 'post_kind';
                    kind.value = 'repost';

                    const repostId = document.createElement('input');
                    repostId.type = 'hidden';
                    repostId.name = 'repost_status_id';
                    repostId.value = statusId;

                    const text = document.createElement('input');
                    text.type = 'hidden';
                    text.name = 'text';
                    text.value = quote;

                    form.appendChild(token);
                    form.appendChild(kind);
                    form.appendChild(repostId);
                    form.appendChild(text);
                    document.body.appendChild(form);
                    form.submit();
                };
            }
        </script>
    @endpush
@endonce
