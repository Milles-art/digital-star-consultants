<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Digital Star Consultants')</title>
    <link href="https://fonts.bunny.net/css?family=outfit:400,500,600,700&display=swap" rel="stylesheet">
    <style>
        *,*::before,*::after{box-sizing:border-box}
        body{margin:0;min-height:100vh;font-family:'Outfit',system-ui,sans-serif;background:#0b1220;color:#0f172a;display:grid;place-items:center;padding:24px}
        .box{width:100%;max-width:420px;background:#fff;border-radius:20px;padding:32px;box-shadow:0 25px 50px rgba(0,0,0,.35)}
        .logo{display:flex;align-items:center;gap:10px;margin-bottom:24px;justify-content:center}
        .mark{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff;display:grid;place-items:center;font-weight:700}
        h1{margin:0 0 6px;font-size:24px;font-weight:700;text-align:center;letter-spacing:-.02em}
        p.sub{margin:0 0 24px;text-align:center;color:#64748b;font-size:14px}
        label{display:block;font-size:13px;font-weight:600;margin-bottom:6px}
        input[type=email],input[type=password],input[type=text]{
            width:100%;padding:12px 14px;border:1px solid #e2e8f0;border-radius:12px;font-size:14px;margin-bottom:16px;outline:none
        }
        input:focus{border-color:#93c5fd;box-shadow:0 0 0 3px rgba(59,130,246,.15)}
        .btn{width:100%;border:0;border-radius:12px;padding:13px;background:#2563eb;color:#fff;font-weight:600;font-size:14px;cursor:pointer}
        .btn:hover{background:#1d4ed8}
        .err{background:#fef2f2;border:1px solid #fecaca;color:#991b1b;padding:10px 12px;border-radius:10px;font-size:13px;margin-bottom:16px}
        .ok{background:#ecfdf5;border:1px solid #a7f3d0;color:#065f46;padding:10px 12px;border-radius:10px;font-size:13px;margin-bottom:16px}
        .foot{margin-top:16px;text-align:center;font-size:13px;color:#64748b}
        a{color:#2563eb;font-weight:600}
    </style>
</head>
<body>
    <div class="box">
        @yield('content')
    </div>
</body>
</html>
