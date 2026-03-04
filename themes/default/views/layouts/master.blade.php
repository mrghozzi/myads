<!DOCTYPE HTML>
<html lang="{{ $site_settings->lang ?? 'en' }}">
<head>
    <title>@yield('title', $site_settings->titer ?? 'MyAds')</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="generator" content="Myads" />
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Favicons -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ theme_asset('img/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ theme_asset('img/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ theme_asset('img/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ theme_asset('img/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ theme_asset('img/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ theme_asset('img/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ theme_asset('img/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ theme_asset('img/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ theme_asset('img/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ theme_asset('img/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ theme_asset('img/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ theme_asset('img/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ theme_asset('img/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ theme_asset('img/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#615dfa">
    <meta name="msapplication-TileImage" content="{{ theme_asset('img/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#615dfa">

    <!-- CSS -->
    @php
        $mode = \Illuminate\Support\Facades\Cookie::get('modedark', 'css');
        $css_path = $mode == 'css_d' ? 'css_d' : 'css';
    @endphp
    <script>
        (function() {
            function readCookie(name) {
                const match = document.cookie.match(new RegExp('(?:^|; )' + name + '=([^;]*)'));
                return match ? decodeURIComponent(match[1]) : null;
            }
            function readStoredMode() {
                try {
                    const value = localStorage.getItem('themeMode');
                    if (value === 'css' || value === 'css_d') {
                        return value;
                    }
                } catch (e) {
                }
                const cookieMode = readCookie('modedark');
                return cookieMode === 'css' || cookieMode === 'css_d' ? cookieMode : null;
            }
            const mode = readStoredMode() || '{{ $css_path }}';
            document.documentElement.dataset.theme = mode;
            window.__themeMode = mode;
        })();
    </script>
    <link id="theme-bootstrap" data-theme-link="true" href="{{ theme_asset($css_path . '/bootstrap.min.css') }}" rel='stylesheet' type='text/css' />
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css" rel='stylesheet' type='text/css' />
    <link id="theme-styles" data-theme-link="true" href="{{ theme_asset($css_path . '/styles.min.css') }}" rel='stylesheet' type='text/css' />
    <link id="theme-prestyle" data-theme-link="true" href="{{ theme_asset($css_path . '/prestyle.css') }}" rel='stylesheet' type='text/css' />
    <link id="theme-simplebar" data-theme-link="true" rel="stylesheet" href="{{ theme_asset($css_path . '/simplebar.css') }}">
    <link id="theme-tiny-slider" data-theme-link="true" rel="stylesheet" href="{{ theme_asset($css_path . '/tiny-slider.css') }}">
    <link id="theme-dataTables" data-theme-link="true" rel="stylesheet" href="{{ theme_asset($css_path . '/dataTables.css') }}">
    <link href="https://use.fontawesome.com/releases/v6.4.2/css/all.css" rel="stylesheet">

    <!-- Fonts -->
    <link href='//fonts.googleapis.com/css?family=Comfortaa:400,700,300' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Muli:400,300,300italic,400italic' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Open+Sans:100,200,300,400,500,600,700,800,900' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Sanchez:400,400italic' rel='stylesheet' type='text/css'>
    <link href='//fonts.googleapis.com/css?family=Open+Sans:400,300,300italic,400italic,600,600italic,700,700italic,800,800italic' rel='stylesheet' type='text/css'>

    <!-- JS -->
    <script type="text/javascript" src="{{ theme_asset('js/jquery-3.6.0.min.js') }}"></script>

    <style>
        /* Fix for header dropdown interaction */
        .header .header-actions .header-dropdown,
        .header .header-actions .header-settings-dropdown {
            z-index: 999999 !important;
        }
        .header-dropdown-trigger,
        .header-settings-dropdown-trigger {
            cursor: pointer;
            position: relative;
            z-index: 10002;
            pointer-events: auto !important;
        }
        .header-dropdown-trigger.active + .header-dropdown,
        .header-settings-dropdown-trigger.active + .header-settings-dropdown {
            pointer-events: auto !important;
        }
        .header-dropdown-trigger.active + .header-dropdown *,
        .header-settings-dropdown-trigger.active + .header-settings-dropdown * {
            pointer-events: auto !important;
        }
        .theme-toggle {
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
        }
        .theme-toggle .theme-toggle-track {
            position: relative;
            width: 54px;
            height: 28px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.16);
            display: inline-flex;
            align-items: center;
            padding: 2px;
            gap: 8px;
            transition: background 0.2s ease;
        }
        .theme-toggle .theme-toggle-thumb {
            position: absolute;
            top: 2px;
            left: 2px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #fff;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
            transition: transform 0.2s ease, background 0.2s ease;
        }
        .theme-toggle .theme-toggle-icon {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.9);
            z-index: 1;
            width: 16px;
            text-align: center;
        }
        .theme-toggle.is-dark .theme-toggle-track {
            background: rgba(97, 93, 250, 0.35);
        }
        .theme-toggle.is-dark .theme-toggle-thumb {
            transform: translateX(26px);
            background: #0f1014;
        }
    </style>

    @stack('head')
</head>
<body data-theme="{{ $css_path }}">

    @include('theme::partials.header.nav')
    @include('theme::partials.header.sidemenu')
    @include('theme::partials.header.desktop_sidebar')
    @include('theme::partials.header.mobile_sidebar')
    @include('theme::partials.header.floaty_bar')

    <div class="content-grid">
        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        <!-- Place footer content here if any -->
    </footer>

    <!-- Scripts -->
    <script src="{{ theme_asset('js/app.js') }}"></script>
    <script src="{{ theme_asset('js/simplebar.min.js') }}"></script>
    <script src="{{ theme_asset('js/tiny-slider.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_accordion.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_dropdown.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_hexagon.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_popup.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_progressBar.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_tab.min.js') }}"></script>
    <script src="{{ theme_asset('js/xm_tooltip.min.js') }}"></script>
    <script src="{{ theme_asset('js/global.hexagons.js') }}"></script>
    <script src="{{ theme_asset('js/global.tooltips.js') }}"></script>
    <script src="{{ theme_asset('js/header.js') }}"></script>
    <script src="{{ theme_asset('js/sidebar.js') }}"></script>
    <script src="{{ theme_asset('js/content.js') }}"></script>
    <script src="{{ theme_asset('js/form.utils.js') }}"></script>
    <script src="{{ theme_asset('js/svg-loader.js') }}"></script>

    <script>
        function applyThemeLinks(mode) {
            document.body.dataset.theme = mode;
            document.documentElement.dataset.theme = mode;
            const links = document.querySelectorAll('link[data-theme-link="true"]');
            links.forEach(function(link) {
                const href = link.getAttribute('href');
                if (!href) {
                    return;
                }
                const nextHref = href.replace(/\/css_d\//, '/' + mode + '/').replace(/\/css\//, '/' + mode + '/');
                if (nextHref !== href) {
                    link.setAttribute('href', nextHref);
                }
            });
            const toggle = document.querySelector('.theme-toggle');
            if (toggle) {
                const isDark = mode === 'css_d';
                toggle.classList.toggle('is-dark', isDark);
                toggle.setAttribute('aria-pressed', isDark ? 'true' : 'false');
                toggle.setAttribute('title', isDark ? 'Light Mode' : 'Dark Mode');
            }
        }

        function setThemeMode(mode, persist = true) {
            applyThemeLinks(mode);
            if (persist) {
                try {
                    localStorage.setItem('themeMode', mode);
                } catch (e) {
                }
                document.cookie = 'modedark=' + mode + ';path=/;max-age=31536000';
            }
            window.__themeMode = mode;
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.querySelector('.theme-toggle');
            const initialMode = window.__themeMode === 'css_d' ? 'css_d' : 'css';
            applyThemeLinks(initialMode);
            requestAnimationFrame(function() {
                applyThemeLinks(initialMode);
            });
            setTimeout(function() {
                applyThemeLinks(initialMode);
            }, 200);
            if (toggle) {
                toggle.classList.toggle('is-dark', initialMode === 'css_d');
                toggle.setAttribute('aria-pressed', initialMode === 'css_d' ? 'true' : 'false');
                toggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    const nextMode = document.body.dataset.theme === 'css_d' ? 'css' : 'css_d';
                    setThemeMode(nextMode);
                });
            }
        });

        window.addEventListener('storage', function(event) {
            if (event.key === 'themeMode' && (event.newValue === 'css' || event.newValue === 'css_d')) {
                setThemeMode(event.newValue, false);
            }
        });

        function initHexagons() {
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

        function focusComment(id) {
            let el = document.getElementById('txt_comment' + id);
            if (el) {
                el.focus();
                el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        }

        function toggleReaction(id, type, reaction) {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch('{{ route("reaction.toggle") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    id: id,
                    type: type,
                    reaction: reaction
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.html) {
                    let prefix = 'reaction-btn-';
                    if (type.includes('comment')) {
                        prefix = 'reaction-btn-comment-';
                    }
                    let btn = document.getElementById(prefix + id);
                    if (btn) {
                        btn.innerHTML = data.html;
                    }
                } else if (data.error) {
                    console.error(data.error);
                    alert('{{ __('messages.error_prefix') }}' + data.error);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function loadComments(id, type, limit = 5) {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let selector = '.post-comment-list-' + id;
            
            return fetch('{{ route("comment.load") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    id: id,
                    type: type,
                    limit: limit
                })
            })
            .then(response => response.text())
            .then(html => {
                let el = document.querySelector(selector);
                if(el) {
                    el.innerHTML = html;
                    initHexagons();
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function deletePost(id, type) {
            if (!confirm('{{ __('messages.confirm_delete') }}')) return;

            let url = '';
            if (type == 'forum' || type == 2 || type == 4 || type == 100) {
                url = '{{ route("forum.delete") }}';
            } else if (type == 'store' || type == 7867) {
                url = '{{ route("store.delete") }}';
            } else if (type == 'directory' || type == 1) {
                url = '{{ route("directory.delete") }}';
            }

            if (!url) {
                console.error('Unknown post type:', type);
                return;
            }

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert('Error: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function buildReportForm(containerId, title) {
            let container = document.getElementById('report' + containerId);
            if (!container) return null;

            let textareaId = 'report_txt_' + containerId;
            let submitId = 'report_submit_' + containerId;
            let closeId = 'report_close_' + containerId;

            container.innerHTML = `
<hr />
<h4><i class="fa fa-flag" aria-hidden="true"></i>&nbsp;${title}</h4>
<br />
<textarea class="quicktext form-control" id="${textareaId}"></textarea>
<hr />
<center>
<div class="btn-group">
<button id="${submitId}" class="btn btn-warning">${REPORT_TEXTS.confirm}</button>&nbsp;
<button id="${closeId}" class="btn btn-danger">${REPORT_TEXTS.close}</button>
</div>
</center>
`;

            return { container, textareaId, submitId, closeId };
        }

        function submitReportForm(form, tpId, sType) {
            let textarea = document.getElementById(form.textareaId);
            if (!textarea) return;

            let reason = textarea.value.trim();
            if (!reason) return;

            form.container.innerHTML = `<hr /><div class="alert alert-warning alert-dismissible fade show" role="alert">${REPORT_TEXTS.pending}<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>`;

            fetch('{{ route("forum.report") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ tp_id: tpId, s_type: sType, txt: reason })
            })
            .then(response => response.json().then(data => ({ ok: response.ok, data })))
            .then(({ ok, data }) => {
                if (!ok || !data.success) {
                    let message = data && data.error ? data.error : REPORT_TEXTS.errorPrefix;
                    form.container.innerHTML = `<hr /><div class="alert alert-danger" role="alert">${message}</div>`;
                }
            })
            .catch(error => {
                form.container.innerHTML = `<hr /><div class="alert alert-danger" role="alert">${REPORT_TEXTS.errorPrefix}</div>`;
                console.error('Error:', error);
            });
        }

        const REPORT_TEXTS = {
            report: @json(__('messages.report')),
            reportAuthor: @json(__('messages.report_author')),
            confirm: @json(__('messages.confirm')),
            close: @json(__('messages.close')),
            pending: @json(__('messages.pending')),
            errorPrefix: @json(__('messages.error_prefix')),
        };

        function reportPost(id, type, containerId = null) {
            let targetId = containerId || id;
            let form = buildReportForm(targetId, REPORT_TEXTS.report);
            if (!form) return;

            document.getElementById(form.submitId).addEventListener('click', function() {
                submitReportForm(form, id, type);
            });
            document.getElementById(form.closeId).addEventListener('click', function() {
                form.container.innerHTML = '';
            });
        }

        function reportUser(uid, containerId = null) {
            let targetId = containerId || uid;
            let form = buildReportForm(targetId, REPORT_TEXTS.reportAuthor);
            if (!form) return;

            document.getElementById(form.submitId).addEventListener('click', function() {
                submitReportForm(form, uid, 99);
            });
            document.getElementById(form.closeId).addEventListener('click', function() {
                form.container.innerHTML = '';
            });
        }

        function sharePost(social, url, title) {
            let shareUrls = {
                facebook: `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url)}`,
                twitter: `https://twitter.com/intent/tweet?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`,
                linkedin: `https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(url)}`,
                telegram: `https://t.me/share/url?url=${encodeURIComponent(url)}&text=${encodeURIComponent(title)}`
            };
            if (shareUrls[social]) {
                window.open(shareUrls[social], '_blank', 'width=600,height=400');
            }
        }

        function postEdit(id, type, extra) {
             if (type == 100) {
                 let container = document.getElementById('post_form' + id);
                 if (!container) return;
                 
                 if (container.querySelector('textarea')) return;

                 let currentText = container.innerText;
                 
                 let html = `
                    <form onsubmit="event.preventDefault(); savePost(${id}, ${type}, this);">
                        <textarea class="form-control" name="txt" style="width:100%; height:100px;">${currentText.trim()}</textarea>
                        <button type="submit" class="btn btn-sm btn-primary mt-2">{{ __('messages.save') }}</button>
                        <button type="button" class="btn btn-sm btn-secondary mt-2" onclick="cancelEdit(${id});">{{ __('messages.cancel') }}</button>
                    </form>
                 `;
                 
                 container.dataset.originalContent = container.innerHTML;
                 container.innerHTML = html;
             } else if (type == 2 || type == 4) {
                 window.location.href = '{{ url("editor") }}/' + id;
             } else if (type == 1) {
                 window.location.href = '{{ url("directory") }}/' + id + '/edit';
             } else if (type == 7867) {
                 if (extra) {
                     window.location.href = '{{ url("store") }}/' + extra + '/update';
                 } else {
                     alert('Please use the edit button on the product page.');
                 }
             }
        }

        function savePost(id, type, form) {
            let txt = form.txt.value;
            fetch('{{ route("forum.update", ":id") }}'.replace(':id', id), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ 
                    name: 'post', 
                    txt: txt,
                    cat: 0 
                })
            })
            .then(response => {
                if(response.ok) {
                    window.location.reload();
                } else {
                    alert('Error saving post');
                }
            });
        }

        function cancelEdit(id) {
            let container = document.getElementById('post_form' + id);
            if (container && container.dataset.originalContent) {
                container.innerHTML = container.dataset.originalContent;
            }
        }

        function postComment(id, type) {
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            let input = document.getElementById('txt_comment' + id);
            if (!input) return;

            let text = input.value;
            if (!text.trim()) return;

            fetch('{{ route("comment.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    id: id,
                    type: type,
                    comment: text
                })
            })
            .then(async response => {
                const fallbackError = @json(__('messages.error_prefix'));
                const contentType = response.headers.get('content-type') || '';

                if (contentType.includes('application/json')) {
                    const data = await response.json();
                    if (!response.ok || data.error) {
                        throw new Error(data.error || fallbackError);
                    }

                    return data.html || '';
                }

                const html = await response.text();
                if (!response.ok) {
                    throw new Error(fallbackError);
                }

                return html;
            })
            .then(html => {
                let el = document.querySelector('.post-comment-list-' + id);
                if (el && html) {
                    el.innerHTML = html;
                    initHexagons();
                }

                input.value = '';
                input.dispatchEvent(new Event('input', { bubbles: true }));
                input.focus();
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || @json(__('messages.error_prefix')));
            });
        }

        function deleteComment(trashid, type) {
            if(!confirm('{{ __("messages.are_you_sure") }}')) return;
            
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            fetch('{{ route("comment.delete") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    trashid: trashid,
                    type: type
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    document.querySelector('.coment' + trashid).remove();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function getCsrfToken() {
            let tokenMeta = document.querySelector('meta[name="csrf-token"]');
            return tokenMeta ? tokenMeta.getAttribute('content') : '';
        }

        function deletePostByUrl(url, id, containerSelector) {
            if (!confirm('{{ __('messages.confirm_delete') }}')) return;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ id: id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (containerSelector) {
                        let container = document.querySelector(containerSelector);
                        if (container) {
                            container.remove();
                            return;
                        }
                    }
                    window.location.reload();
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        function reportPostByUrl(url, id, type) {
            let reason = prompt('{{ __('messages.report_reason') }}');
            if (!reason) return;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },
                body: JSON.stringify({ tp_id: id, s_type: type, txt: reason })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('{{ __('messages.report_sent') }}');
                } else if (data.error) {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        }

        document.addEventListener('click', function(event) {
            let target = event.target.closest('[data-post-action]');
            if (!target) return;

            let action = target.dataset.postAction;
            let id = target.dataset.postId;
            let type = target.dataset.postType;
            let editUrl = target.dataset.editUrl;
            let deleteUrl = target.dataset.deleteUrl;
            let reportUrl = target.dataset.reportUrl;
            let containerSelector = target.dataset.postContainer;

            if (action === 'edit' && editUrl) {
                event.preventDefault();
                window.location.href = editUrl;
                return;
            }

            if (action === 'delete' && deleteUrl && id) {
                event.preventDefault();
                deletePostByUrl(deleteUrl, id, containerSelector);
                return;
            }

            if (action === 'report' && reportUrl && id && type) {
                event.preventDefault();
                reportPostByUrl(reportUrl, id, type);
            }
        });
    </script>
    
    @include('theme::partials._cookie_consent')

    @stack('scripts')
</body>
</html>
