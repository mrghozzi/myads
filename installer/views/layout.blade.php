<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MyAds Installer')</title>
    <meta name="robots" content="noindex, nofollow">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root {
            --installer-bg: #0b0e14;
            --installer-card: #141821;
            --installer-card-border: #1e2433;
            --installer-primary: #6366f1;
            --installer-primary-hover: #818cf8;
            --installer-success: #22c55e;
            --installer-danger: #ef4444;
            --installer-warning: #f59e0b;
            --installer-text: #e2e8f0;
            --installer-text-muted: #94a3b8;
            --installer-input-bg: #0f1219;
            --installer-input-border: #1e2433;
            --installer-step-inactive: #334155;
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--installer-bg);
            color: var(--installer-text);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            margin: 0;
            background-image:
                radial-gradient(ellipse at 20% 50%, rgba(99, 102, 241, 0.08) 0%, transparent 50%),
                radial-gradient(ellipse at 80% 20%, rgba(139, 92, 246, 0.06) 0%, transparent 50%);
        }

        .installer-wrapper {
            width: 100%;
            max-width: 640px;
        }

        /* ── Brand ── */
        .installer-brand {
            text-align: center;
            margin-bottom: 2rem;
        }
        .installer-brand h1 {
            font-size: 1.75rem;
            font-weight: 700;
            background: linear-gradient(135deg, var(--installer-primary), #a78bfa);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin: 0;
        }
        .installer-brand p {
            color: var(--installer-text-muted);
            font-size: .85rem;
            margin-top: .25rem;
        }

        /* ── Stepper ── */
        .installer-steps {
            display: flex;
            justify-content: center;
            gap: .25rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
        .installer-step {
            display: flex;
            align-items: center;
            gap: .35rem;
            font-size: .72rem;
            font-weight: 500;
            color: var(--installer-step-inactive);
            padding: .35rem .65rem;
            border-radius: 999px;
            transition: all .25s ease;
        }
        .installer-step.active {
            background: rgba(99, 102, 241, .15);
            color: var(--installer-primary-hover);
        }
        .installer-step.done {
            color: var(--installer-success);
        }
        .installer-step .step-dot {
            width: 6px; height: 6px;
            border-radius: 50%;
            background: currentColor;
            flex-shrink: 0;
        }

        /* ── Card ── */
        .installer-card {
            background: var(--installer-card);
            border: 1px solid var(--installer-card-border);
            border-radius: 1rem;
            padding: 2rem;
            box-shadow: 0 4px 24px rgba(0,0,0,.25);
        }
        .installer-card h2 {
            font-size: 1.25rem;
            font-weight: 600;
            margin: 0 0 .35rem;
        }
        .installer-card .subtitle {
            color: var(--installer-text-muted);
            font-size: .85rem;
            margin-bottom: 1.5rem;
        }

        /* ── Form ── */
        .form-label {
            font-size: .8rem;
            font-weight: 500;
            color: var(--installer-text-muted);
            margin-bottom: .35rem;
        }
        .form-control {
            background: var(--installer-input-bg);
            border: 1px solid var(--installer-input-border);
            color: var(--installer-text);
            border-radius: .5rem;
            padding: .6rem .85rem;
            font-size: .875rem;
            transition: border-color .2s;
        }
        .form-control:focus {
            background: var(--installer-input-bg);
            border-color: var(--installer-primary);
            color: var(--installer-text);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, .15);
        }
        .form-control::placeholder { color: #475569; }

        /* ── Buttons ── */
        .btn-installer {
            background: var(--installer-primary);
            color: #fff;
            border: none;
            border-radius: .5rem;
            padding: .65rem 1.5rem;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .btn-installer:hover {
            background: var(--installer-primary-hover);
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-installer:disabled {
            opacity: .5;
            cursor: not-allowed;
            transform: none;
        }
        .btn-outline-installer {
            background: transparent;
            color: var(--installer-text-muted);
            border: 1px solid var(--installer-card-border);
            border-radius: .5rem;
            padding: .65rem 1.5rem;
            font-size: .875rem;
            font-weight: 500;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .btn-outline-installer:hover {
            border-color: var(--installer-primary);
            color: var(--installer-primary-hover);
        }
        .btn-success-installer {
            background: var(--installer-success);
            color: #fff;
            border: none;
            border-radius: .5rem;
            padding: .65rem 1.5rem;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .btn-success-installer:hover {
            background: #16a34a;
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-warning-installer {
            background: var(--installer-warning);
            color: #000;
            border: none;
            border-radius: .5rem;
            padding: .65rem 1.5rem;
            font-size: .875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
        }
        .btn-warning-installer:hover {
            background: #d97706;
            color: #000;
            transform: translateY(-1px);
        }

        /* ── Alerts ── */
        .alert-installer {
            border-radius: .5rem;
            padding: .75rem 1rem;
            font-size: .85rem;
            margin-bottom: 1.25rem;
            border: 1px solid;
        }
        .alert-installer.alert-danger {
            background: rgba(239, 68, 68, .1);
            border-color: rgba(239, 68, 68, .3);
            color: #fca5a5;
        }
        .alert-installer.alert-success {
            background: rgba(34, 197, 94, .1);
            border-color: rgba(34, 197, 94, .3);
            color: #86efac;
        }
        .alert-installer.alert-warning {
            background: rgba(245, 158, 11, .1);
            border-color: rgba(245, 158, 11, .3);
            color: #fcd34d;
        }

        /* ── Actions Bar ── */
        .installer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1.5rem;
            gap: 1rem;
        }

        /* ── Check List ── */
        .check-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: .6rem 0;
            border-bottom: 1px solid var(--installer-card-border);
            font-size: .85rem;
        }
        .check-item:last-child { border-bottom: none; }
        .check-item .status-ok { color: var(--installer-success); }
        .check-item .status-fail { color: var(--installer-danger); }

        /* ── Log ── */
        .update-log {
            background: var(--installer-input-bg);
            border: 1px solid var(--installer-card-border);
            border-radius: .5rem;
            padding: 1rem;
            max-height: 280px;
            overflow-y: auto;
            font-size: .8rem;
            line-height: 1.6;
            margin-top: 1rem;
        }
        .update-log::-webkit-scrollbar { width: 4px; }
        .update-log::-webkit-scrollbar-track { background: transparent; }
        .update-log::-webkit-scrollbar-thumb { background: var(--installer-card-border); border-radius: 4px; }

        /* ── Footer ── */
        .installer-footer {
            text-align: center;
            margin-top: 1.5rem;
            font-size: .75rem;
            color: var(--installer-step-inactive);
        }

        /* ── Spinner ── */
        .spinner-border-sm { width: 1rem; height: 1rem; }

        @media (max-width: 576px) {
            .installer-card { padding: 1.5rem 1.25rem; }
            .installer-steps { gap: .15rem; }
            .installer-step { font-size: .65rem; padding: .3rem .5rem; }
        }
    </style>
</head>
<body>
    <div class="installer-wrapper">
        <div class="installer-brand">
            <h1><i class="fas fa-ad"></i> MyAds</h1>
            <p>@yield('brand-subtitle', 'Installation Wizard')</p>
        </div>

        @hasSection('steps')
            <div class="installer-steps">
                @yield('steps')
            </div>
        @endif

        <div class="installer-card">
            @if(session('error'))
                <div class="alert-installer alert-danger">
                    <i class="fas fa-exclamation-circle me-1"></i> {{ session('error') }}
                </div>
            @endif
            @if(session('success'))
                <div class="alert-installer alert-success">
                    <i class="fas fa-check-circle me-1"></i> {{ session('success') }}
                </div>
            @endif
            @if($errors->any())
                <div class="alert-installer alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>

        <div class="installer-footer">
            MyAds v4.0.0 &mdash; &copy; {{ date('Y') }}
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
