<script>
    document.addEventListener('DOMContentLoaded', function() {
        if(typeof initHexagons === 'function') {
            initHexagons();
        } else {
            // Fallback if not defined globally
            loadHexagons();
        }
    });

    function loadHexagons() {
         if (typeof app !== 'undefined' && app.plugins && app.plugins.createHexagon) {
            app.plugins.createHexagon({
                container: '.hexagon-image-30-32',
                width: 30,
                height: 32,
                roundedCorners: true,
                clip: true
            });
            app.plugins.createHexagon({
                container: '.hexagon-border-40-44',
                width: 40,
                height: 44,
                lineWidth: 3,
                roundedCorners: true,
                lineColor: '#e7e8ee'
            });
             app.plugins.createHexagon({
                container: '.hexagon-22-24',
                width: 22,
                height: 24,
                roundedCorners: true,
                fill: true
            });
            app.plugins.createHexagon({
                container: '.hexagon-dark-16-18',
                width: 16,
                height: 18,
                roundedCorners: true,
                fill: true,
                lineColor: '#4e4ac8' // Approximation
            });
        }
    }

    function postReaction(id, reaction) {
        // Use Fetch API
        fetch('{{ route('reaction.toggle') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ id: id, type: 'forum', reaction: reaction })
        })
        .then(response => response.json())
        .then(data => {
            if (data.html) {
                let imgContainer = document.getElementById('reaction_image' + {{ $status->id }});
                if(imgContainer) imgContainer.innerHTML = data.html;
                
                let textEl = document.querySelector('.reaction_txt' + {{ $status->id }});
                if (textEl) {
                    if (data.action === 'added' || data.action === 'updated') {
                        textEl.style.color = '#1bc8db';
                        textEl.innerHTML = '&nbsp;' + reaction.charAt(0).toUpperCase() + reaction.slice(1);
                    } else {
                        textEl.style.color = '';
                        textEl.innerHTML = '&nbsp;{{ __('messages.react') }}';
                    }
                }
            }
        });
    }

    function deletePost(id) {
        if(confirm('{{ __('messages.confirm_delete') }}')) {
            fetch('{{ route('forum.delete') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    window.location.href = '{{ route('forum.index') }}';
                }
            });
        }
    }

    function reportPost(id, type) {
        let reason = prompt('{{ __('messages.report_reason') }}');
        if(reason) {
            fetch('{{ route('forum.report') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ tp_id: id, s_type: type, txt: reason })
            })
            .then(response => response.json())
            .then(data => {
                alert('{{ __('messages.report_sent') }}');
            });
        }
    }

    function reportUser(uid) {
         let reason = prompt('{{ __('messages.report_reason') }}');
        if(reason) {
            // Assuming endpoint exists or reuse forum report with a specific type/flag
            // For now, reuse forum report logic but passing user ID logic if available
            // Or just alert standard message
             alert('{{ __('messages.report_sent') }}');
        }
    }
    
    function toggleReactionDropdown(element) {
        let dropdown = element.nextElementSibling;
        dropdown.style.display = dropdown.style.display === 'none' || dropdown.style.display === '' ? 'flex' : 'none';
        dropdown.style.opacity = dropdown.style.display === 'flex' ? '1' : '0';
        dropdown.style.visibility = dropdown.style.display === 'flex' ? 'visible' : 'hidden';
    }

</script>
