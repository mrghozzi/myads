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

    if (!container || !trigger) return;

    const observer = new IntersectionObserver((entries) => {
        if (entries[0].isIntersecting && !isLoading) {
            loadMore();
        }
    }, { rootMargin: '200px' });

    observer.observe(trigger);

    function loadMore() {
        if (isLoading) return;
        const url = trigger.getAttribute('data-next-page');
        if (!url) return;

        isLoading = true;
        
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                trigger.insertAdjacentHTML('beforebegin', data.html);
            }

            if (data.next_page_url) {
                trigger.setAttribute('data-next-page', data.next_page_url);
                isLoading = false; 
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
