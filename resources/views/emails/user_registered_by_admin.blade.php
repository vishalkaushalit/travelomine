<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Account Created</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background: #ffffff; padding: 30px; border-radius: 8px;">
        <h2>Welcome, {{ $user->name }}!</h2>

        <p>Your account has been successfully created on <strong>Travelomine</strong>.</p>
        <p>Your role is: <strong>{{ ucfirst($user->role) }}</strong></p>

        <h3>Login Instructions:</h3>
        <ul style="max-width: 600px;list-style: none; padding: 0;">
            <li><strong>User ID:</strong> {{ $user->agent_custom_id }}</li>
            <li><strong>Email:</strong> {{ $user->email }}</li>
            <li><strong>Login Link:</strong> <a href="{{ $loginUrl }}" target="_blank">{{ $loginUrl }}</a></li>
        </ul>

        <p>Your password will be provided by your administrator or IT.</p>
        <p>Please contact <strong>admin</strong> or email <a href="mailto:it@callinggenie.com">it@callinggenie.com</a> to receive your password.</p>

        <p style="margin-top: 30px;">Thanks,<br><strong>Team Travelomine !</strong></p>
    </div>
</body>
</html>
 