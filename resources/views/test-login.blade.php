<!DOCTYPE html>
<html>
<head>
    <title>Test Login</title>
    <style>
        body { font-family: Arial; padding: 50px; }
        input { display: block; margin: 10px 0; padding: 8px; width: 300px; }
        button { padding: 10px 20px; background: blue; color: white; border: none; cursor: pointer; }
        .container { max-width: 400px; margin: auto; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Digital Star Consultants - Test Login</h2>

        <form method="POST" action="/login">
            @csrf
            <input type="email" name="email" placeholder="Email" value="admin@example.com">
            <input type="password" name="password" placeholder="Password" value="password123">
            <button type="submit">Login</button>
        </form>

        @if(session('status'))
            <p style="color: green;">{{ session('status') }}</p>
        @endif

        @if($errors->any())
            <p style="color: red;">{{ $errors->first() }}</p>
        @endif
    </div>
</body>
</html>
