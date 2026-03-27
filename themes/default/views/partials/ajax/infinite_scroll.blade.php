@if($paginator->hasMorePages())
    <div id="infinite-scroll-trigger" data-next-page="{{ $paginator->nextPageUrl() }}" class="text-center w-100 mt-4 mb-4">
        @include('theme::partials.ajax.skeleton')
    </div>
@endif

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.getElementById('infinite-scroll-container');
    let trigger = document.getElementById('infinite-scroll-trigger');
    let isLoading = false;
    const expiredMessage = @json(__('messages.error_419_text'));
    const genericErrorMessage = @json(__('messages.error_500_text'));

    if (!container || !trigger) return;

    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !isLoading) {
            loadMore();
        }
    }, { rootMargin: '200px' });

    observer.observe(trigger);

    function normalizeNextPageUrl(url) {
        if (!url) {
            return '';
        }

        try {
            const resolvedUrl = new URL(url, window.location.href);
            return window.location.pathname + resolvedUrl.search;
        } catch (error) {
            return url;
        }
    }

    async function readPayload(response) {
        const responseText = await response.text();
        const normalizedText = (responseText || '').replace(/^\uFEFF/, '').trim();

        if (!normalizedText) {
            return {};
        }

        try {
            return JSON.parse(normalizedText);
        } catch (error) {
            throw new Error(response.status === 419 ? expiredMessage : genericErrorMessage);
        }
    }

    function loadMore() {
        if (isLoading) return;
        const url = normalizeNextPageUrl(trigger.getAttribute('data-next-page'));
        if (!url) return;

        isLoading = true;
        
        fetch(url, {
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(async (response) => {
            const data = await readPayload(response);

            if (!response.ok) {
                throw new Error(data.message || (response.status === 419 ? expiredMessage : genericErrorMessage));
            }

            return data;
        })
        .then(data => {
            if (data.html) {
                trigger.insertAdjacentHTML('beforebegin', data.html);

                if (typeof window.runAfterInfiniteScrollRender === 'function') {
                    window.runAfterInfiniteScrollRender(container);
                } else if (typeof window.afterInfiniteScrollRender === 'function') {
                    window.afterInfiniteScrollRender(container);
                }
            }

            if (data.next_page_url) {
                trigger.setAttribute('data-next-page', normalizeNextPageUrl(data.next_page_url));
                isLoading = false; 
                
                // Force IntersectionObserver to re-evaluate visibility
                // because if the fetched items don't push the trigger 
                // out of the viewport, the observer won't fire again.
                observer.unobserve(trigger);
                setTimeout(() => {
                    observer.observe(trigger);
                }, 100);
            } else {
                observer.unobserve(trigger);
                trigger.remove();
            }
        })
        .catch(error => {
            console.error('Infinite Scroll Error:', error);
            isLoading = false;
        });
    }
});
</script>
@endpush
