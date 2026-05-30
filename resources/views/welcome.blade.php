<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <div id="app">
        <header>
            <h1>Welcome to Our Application</h1>
        </header>
        <main>
            <p>This is a simple Laravel Blade template.</p>
        </main>
        <footer>
            <p>&copy; {{ date('Y') }} Your Company. All rights reserved.</p>
        </footer>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>