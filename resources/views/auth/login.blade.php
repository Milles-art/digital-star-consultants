<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login | {{ config('app.name', 'Digital Star Consultants') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-body min-h-screen">
    <div class="admin-aurora" aria-hidden="true"></div>

    <main class="grid min-h-screen place-items-center px-5 py-10">
        <section class="admin-login-card reveal">
            <div class="text-center">
                <span class="admin-login-mark">DS</span>
                <p class="admin-kicker mt-6">Operations console</p>
                <h1 class="mt-2 text-3xl font-black text-ink">Welcome back</h1>
                <p class="mt-3 text-sm text-muted">Sign in to manage submissions, services, staff, reports, and customer messages.</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="admin-form-stack mt-8" data-login>
                @csrf
                <div>
                    <label class="admin-label" for="email">Email</label>
                    <input class="admin-field" id="email" name="email" type="email" value="{{ old('email') }}" autocomplete="email" required autofocus>
                </div>
                <div>
                    <label class="admin-label" for="password">Password</label>
                    <input class="admin-field" id="password" name="password" type="password" autocomplete="current-password" required>
                </div>
                <label class="admin-check mt-0">
                    <input type="checkbox" name="remember" value="1">
                    Remember this device
                </label>
                <button class="admin-button admin-button-dark w-full" type="submit">Sign in</button>
            </form>
        </section>
    </main>
</body>
</html>
