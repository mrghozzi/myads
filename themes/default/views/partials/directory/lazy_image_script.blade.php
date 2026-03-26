<script>
document.addEventListener('DOMContentLoaded', function() {
    if (!('IntersectionObserver' in window)) return;

    const lazyImageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const element = entry.target;
                const fetchUrl = element.dataset.lazyFetchUrl;
                
                if (fetchUrl) {
                    fetch(fetchUrl)
                        .then(response => response.json())
                        .then(data => {
                            if (data.image_url) {
                                if (element.tagName === 'IMG') {
                                    element.src = data.image_url;
                                } else {
                                    element.style.backgroundImage = `url(${data.image_url})`;
                                }
                                element.classList.add('loaded');
                            }
                        })
                        .catch(err => console.error('Error fetching directory image:', err));
                }
                
                observer.unobserve(element);
            }
        });
    }, {
        rootMargin: '100px 0px',
        threshold: 0.01
    });

    document.querySelectorAll('[data-lazy-fetch-url]').forEach(el => {
        lazyImageObserver.observe(el);
    });
});
</script>

<style>
[data-lazy-fetch-image] {
    transition: opacity 0.3s ease-in-out;
}
[data-lazy-fetch-image]:not(.loaded) {
    /* Optional: any style for placeholder state */
}
</style>
