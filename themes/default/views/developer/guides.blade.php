@extends('theme::layouts.master')

@section('title', __('messages.dev_guides'))

@push('head')
    @include('theme::developer.partials.styles')
    <style>
        .dev-guide-container {
            display: grid;
            gap: 28px;
        }
        .dev-guide-header {
            padding: 40px;
            border-radius: 24px;
            background: var(--dev-surface-accent);
            border: 1px solid var(--dev-border);
            margin-bottom: 20px;
            position: relative;
            overflow: hidden;
        }
        .dev-guide-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: radial-gradient(circle, var(--dev-accent) 0%, transparent 70%);
            opacity: 0.1;
            filter: blur(40px);
        }
        .dev-toc-card {
            position: sticky;
            top: 20px;
        }
        .dev-toc-link {
            display: block;
            padding: 12px 18px;
            color: var(--dev-text);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.9rem;
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .dev-toc-link:hover {
            background: rgba(97, 93, 250, 0.05);
            color: var(--dev-accent);
            padding-inline-start: 22px;
        }
        .dev-toc-link.is-active {
            background: var(--dev-surface-accent);
            color: var(--dev-accent);
        }
        .dev-guide-section {
            padding: 34px;
            margin-bottom: 24px;
        }
        .dev-step-badge {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: var(--dev-accent);
            color: #fff;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.85rem;
            margin-bottom: 16px;
        }
        .dev-lang-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: -1px;
            position: relative;
            z-index: 2;
            overflow-x: auto;
            padding-bottom: 4px;
        }
        .dev-lang-tab {
            padding: 10px 18px;
            background: var(--dev-surface-soft);
            border: 1px solid var(--dev-border);
            border-bottom: 0;
            border-radius: 14px 14px 0 0;
            color: var(--dev-muted);
            font-weight: 700;
            font-size: 0.8rem;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        .dev-lang-tab:hover {
            color: var(--dev-title);
            background: var(--dev-surface);
        }
        .dev-lang-tab.is-active {
            background: var(--dev-code-bg);
            border-color: var(--dev-code-border);
            color: #fff;
        }
        .dev-code-container {
            border-radius: 0 18px 18px 18px;
            margin-top: 0;
        }
        .dev-guide-nav-mobile {
            display: none;
        }
        @media screen and (max-width: 1024px) {
            .dev-guide-nav-mobile {
                display: block;
                margin-bottom: 20px;
            }
        }
    </style>
@endpush

@section('content')
<div class="dev-guide-header">
    <p class="dev-kicker">{{ __('messages.dev_integration_guide') }}</p>
    <h1 class="dev-title" style="font-size: 2rem; margin-bottom: 12px;">{{ __('messages.dev_getting_started') }}</h1>
    <p class="dev-summary-copy" style="max-width: 700px;">{{ __('messages.dev_guides_intro') }}</p>
</div>

<div class="grid grid-3-9 mobile-prefer-content">
    <div class="grid-column">
        <div class="dev-side-stack">
            @include('theme::developer.partials.nav', ['active' => 'guides'])

            <div class="widget-box dev-panel dev-toc-card d-none d-lg-block">
                <p class="widget-box-title">{{ __('messages.information') }}</p>
                <div class="widget-box-content" style="padding: 14px;">
                    <nav class="dev-toc">
                        <a href="#step-1" class="dev-toc-link">{{ __('messages.dev_step_1_title') }}</a>
                        <a href="#step-2" class="dev-toc-link">{{ __('messages.dev_step_2_title') }}</a>
                        <a href="#step-3" class="dev-toc-link">{{ __('messages.dev_step_3_title') }}</a>
                        <a href="#code-samples" class="dev-toc-link">{{ __('messages.dev_code_examples') }}</a>
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <div class="grid-column">
        <div class="dev-guide-container">
            <section id="step-1" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">1</span>
                <h2 class="dev-section-title">{{ __('messages.dev_step_1_title') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_step_1_desc') }}</p>
                
                <div class="dev-note dev-note--info" style="margin-top: 20px;">
                    <p><strong>{{ __('messages.info') }}:</strong> {{ __('messages.dev_create_help') }}</p>
                </div>

                <div class="dev-rule-list">
                    <div class="dev-rule-item">
                        <strong>{{ __('messages.client_id') }}</strong>
                        <span class="dev-help-text">{{ __('messages.dev_credentials_help') }}</span>
                    </div>
                </div>
            </section>

            <section id="step-2" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">2</span>
                <h2 class="dev-section-title">{{ __('messages.dev_step_2_title') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_step_2_desc') }}</p>
                
                <div class="dev-code-block">
                    <div class="dev-code-toolbar">
                        <span>Authorization URL</span>
                        <button type="button" class="dev-copy-btn js-dev-copy" data-copy="{{ url('/oauth/authorize') }}?client_id=YOUR_CLIENT_ID&redirect_uri=YOUR_URL&response_type=code&scope=dev_scope_identity_read">
                            <i class="fa fa-copy"></i>
                        </button>
                    </div>
                    <pre><code>{{ url('/oauth/authorize') }}?
client_id=YOUR_CLIENT_ID&
redirect_uri=YOUR_URL&
response_type=code&
scope=dev_scope_identity_read</code></pre>
                </div>
            </section>

            <section id="step-3" class="widget-box dev-panel dev-guide-section">
                <span class="dev-step-badge">3</span>
                <h2 class="dev-section-title">{{ __('messages.dev_step_3_title') }}</h2>
                <p class="dev-card-copy" style="margin-top: 14px;">{{ __('messages.dev_step_3_desc') }}</p>
                
                <div id="code-samples" style="margin-top: 30px;">
                    <div class="dev-surface-header" style="margin-bottom: 20px;">
                        <h3 class="dev-card-title">{{ __('messages.dev_code_examples') }}</h3>
                        <span class="dev-mini-chip">{{ __('messages.dev_all_languages') }}</span>
                    </div>

                    <div class="dev-lang-tabs">
                        <div class="dev-lang-tab is-active" data-lang="php">PHP</div>
                        <div class="dev-lang-tab" data-lang="node">Node.js</div>
                        <div class="dev-lang-tab" data-lang="python">Python</div>
                        <div class="dev-lang-tab" data-lang="csharp">C#</div>
                        <div class="dev-lang-tab" data-lang="curl">cURL</div>
                    </div>

                    <div class="dev-code-block dev-code-container">
                        <div class="js-lang-content" data-lang="php">
                            <div class="dev-code-toolbar">
                                <span>PHP (cURL)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-php">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-php">&lt;?php
$clientId = 'YOUR_CLIENT_ID';
$clientSecret = 'YOUR_CLIENT_SECRET';
$code = $_GET['code'];

$ch = curl_init('{{ url('/oauth/token') }}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, [
    'grant_type' => 'authorization_code',
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'code' => $code,
    'redirect_uri' => 'YOUR_REDIRECT_URI'
]);

$response = json_decode(curl_exec($ch), true);
$accessToken = $response['access_token'];

// Fetch User Identity
$ch = curl_init('{{ url('/api/user') }}');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken,
    'Accept: application/json'
]);

$user = json_decode(curl_exec($ch), true);
print_r($user);
?&gt;</code></pre>
                        </div>

                        <div class="js-lang-content d-none" data-lang="node">
                            <div class="dev-code-toolbar">
                                <span>Node.js (Axios)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-node">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-node">const axios = require('axios');

async function getAccessToken(code) {
    const response = await axios.post('{{ url('/oauth/token') }}', {
        grant_type: 'authorization_code',
        client_id: 'YOUR_CLIENT_ID',
        client_secret: 'YOUR_CLIENT_SECRET',
        code: code,
        redirect_uri: 'YOUR_REDIRECT_URI'
    });
    
    return response.data.access_token;
}

async function getUser(accessToken) {
    const response = await axios.get('{{ url('/api/user') }}', {
        headers: {
            'Authorization': `Bearer ${accessToken}`,
            'Accept': 'application/json'
        }
    });
    
    return response.data;
}</code></pre>
                        </div>

                        <div class="js-lang-content d-none" data-lang="python">
                            <div class="dev-code-toolbar">
                                <span>Python (Requests)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-python">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-python">import requests

def get_access_token(code):
    url = "{{ url('/oauth/token') }}"
    data = {
        'grant_type': 'authorization_code',
        'client_id': 'YOUR_CLIENT_ID',
        'client_secret': 'YOUR_CLIENT_SECRET',
        'code': code,
        'redirect_uri': 'YOUR_REDIRECT_URI'
    }
    response = requests.post(url, data=data)
    return response.json().get('access_token')

def get_user(access_token):
    url = "{{ url('/api/user') }}"
    headers = {
        'Authorization': f'Bearer {access_token}',
        'Accept': 'application/json'
    }
    response = requests.get(url, headers=headers)
    return response.json()</code></pre>
                        </div>

                        <div class="js-lang-content d-none" data-lang="csharp">
                            <div class="dev-code-toolbar">
                                <span>C# (HttpClient)</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-csharp">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-csharp">using System.Net.Http;
using System.Threading.Tasks;

public async Task&lt;string&gt; GetAccessToken(string code) {
    var client = new HttpClient();
    var values = new Dictionary&lt;string, string&gt; {
        { "grant_type", "authorization_code" },
        { "client_id", "YOUR_CLIENT_ID" },
        { "client_secret", "YOUR_CLIENT_SECRET" },
        { "code", code },
        { "redirect_uri", "YOUR_REDIRECT_URI" }
    };

    var content = new FormUrlEncodedContent(values);
    var response = await client.PostAsync("{{ url('/oauth/token') }}", content);
    var responseString = await response.Content.ReadAsStringAsync();
    // Parse JSON for access_token...
    return responseString;
}</code></pre>
                        </div>

                        <div class="js-lang-content d-none" data-lang="curl">
                            <div class="dev-code-toolbar">
                                <span>cURL</span>
                                <button type="button" class="dev-copy-btn js-dev-copy" data-copy-id="code-curl">
                                    <i class="fa fa-copy"></i>
                                </button>
                            </div>
<pre><code id="code-curl"># 1. Exchange code for token
curl -X POST {{ url('/oauth/token') }} \
     -d "grant_type=authorization_code" \
     -d "client_id=YOUR_CLIENT_ID" \
     -d "client_secret=YOUR_CLIENT_SECRET" \
     -d "code=AUTHORIZATION_CODE" \
     -d "redirect_uri=YOUR_REDIRECT_URI"

# 2. Call API with token
curl -H "Authorization: Bearer YOUR_ACCESS_TOKEN" \
     -H "Accept: application/json" \
     {{ url('/api/user') }}</code></pre>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    @include('theme::developer.partials.scripts')
    <script>
        document.querySelectorAll('.dev-lang-tab').forEach(tab => {
            tab.addEventListener('click', () => {
                const lang = tab.getAttribute('data-lang');
                
                // Update tabs
                document.querySelectorAll('.dev-lang-tab').forEach(t => t.classList.remove('is-active'));
                tab.classList.add('is-active');
                
                // Update content
                document.querySelectorAll('.js-lang-content').forEach(content => {
                    if (content.getAttribute('data-lang') === lang) {
                        content.classList.remove('d-none');
                    } else {
                        content.classList.add('d-none');
                    }
                });
            });
        });

        // Copy button enhancement for specific IDs
        document.querySelectorAll('.js-dev-copy').forEach(btn => {
            const copyId = btn.getAttribute('data-copy-id');
            if (copyId) {
                btn.addEventListener('click', () => {
                    const code = document.getElementById(copyId).innerText;
                    navigator.clipboard.writeText(code).then(() => {
                        btn.setAttribute('data-copied', 'true');
                        btn.innerHTML = '<i class="fa fa-check"></i>';
                        setTimeout(() => {
                            btn.setAttribute('data-copied', 'false');
                            btn.innerHTML = '<i class="fa fa-copy"></i>';
                        }, 2000);
                    });
                });
            }
        });
    </script>
@endpush
