<!doctype html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Management Login · Digital Star</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-login-body">
    <div class="admin-login-shell">
        <section class="admin-login-brand-panel">
            <a class="brand" href="{{ route('home') }}">
                <span class="brand-mark"><img src="{{ asset('images/digital-star-mark.svg') }}" alt="Digital Star Consultants star mark"></span>
                <span><b>DIGITAL STAR</b><small>CONSULTANTS</small></span>
            </a>
            <div class="admin-login-brand-copy">
                <span class="eyebrow">MANAGEMENT PORTAL</span>
                <h1>Run the work.<br><em>Keep it moving.</em></h1>
                <p>Manage applications, services, customers, staff and operations from one secure workspace.</p>
                <div class="admin-login-trust">
                    <span>✓ Requests & applications</span>
                    <span>✓ Service catalogue</span>
                    <span>✓ Team operations</span>
                </div>
            </div>
        </section>

        <section class="admin-login-card-wrap">
            <div class="admin-login-card">
                <div class="login-card-top">
                    <div class="login-lock">DS</div>
                    <span class="secure-pill"><i></i> Secure access</span>
                </div>

                <span class="eyebrow">MANAGEMENT SIGN IN</span>
                <h2>{{ !empty($alreadyAuthenticated) ? 'Management session active.' : 'Welcome back.' }}</h2>
                <p class="login-intro">
                    @if (!empty($alreadyAuthenticated))
                        You are already signed in as <strong>{{ auth()->user()->name }}</strong>. If the dashboard is unavailable, sign out here and return to a clean login session.
                    @else
                        Use your management account to access the Digital Star operations dashboard.
                    @endif
                </p>

                @if (!empty($alreadyAuthenticated))
                    <form method="POST" action="{{ route('admin.logout') }}" class="admin-login-form" style="margin-top:24px">
                        @csrf
                        <button class="button button-yellow button-wide admin-login-submit" type="submit">
                            Sign out of management session <span>↗</span>
                        </button>
                    </form>
                @else

                @if ($errors->any())
                    <div class="login-alert" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login.store') }}" class="admin-login-form">
                    @csrf
                    <label>
                        Email address
                        <span class="login-input-wrap">
                            <span>✉</span>
                            <input id="admin-email" type="email" name="email" value="{{ old('email') }}" autocomplete="username" required autofocus placeholder="you@digitalstar.co.tz">
                        </span>
                    </label>

                    <label>
                        Password
                        <span class="login-input-wrap">
                            <span>⌑</span>
                            <input id="admin-password" type="password" name="password" autocomplete="current-password" required placeholder="Enter your password">
                            <button type="button" class="password-toggle" data-password-toggle="#admin-password" aria-label="Show password">Show</button>
                        </span>
                    </label>

                    <div class="login-options">
                        <label class="remember-check"><input type="checkbox" name="remember" value="1"> <span>Keep me signed in</span></label>
                        <a href="{{ route('password.request') }}">Forgot password?</a>
                    </div>

                    <button id="admin-login-submit" class="button button-yellow button-wide admin-login-submit" type="submit">
                        Enter management portal <span>↗</span>
                    </button>
                </form>
                @endif

                <div class="admin-login-footer">
                    <a href="{{ route('login') }}">Staff login</a>
                    <span>·</span>
                    <a href="{{ route('home') }}">Back to website</a>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.querySelector('[data-password-toggle]')?.addEventListener('click', function () {
            const input = document.querySelector(this.dataset.passwordToggle);
            const visible = input.type === 'text';
            input.type = visible ? 'password' : 'text';
            this.textContent = visible ? 'Show' : 'Hide';
            this.setAttribute('aria-label', visible ? 'Show password' : 'Hide password');
        });

        document.querySelector('.admin-login-form')?.addEventListener('submit', function () {
            const button = document.getElementById('admin-login-submit');
            if (!button) return;
            button.disabled = true;
            button.innerHTML = 'Signing in…';
        });
    </script>
</body>
</html>
