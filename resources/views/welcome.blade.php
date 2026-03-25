<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
</head>
<body>
    @if (Auth::check())
        Welcome, {{ Auth::user()->name }}!
        <!-- Add any additional content here -->
    @else
        Please <a href="{{ route('agent.login') }}">Login</a> 
    @endif
</body>
</html>
