<!DOCTYPE html>
<html lang="en-US">
<head>
    <meta charset="utf-8">
    <title>{{ config('app.name') }}</title>
</head>
<body>
<p>
    <b> Hello,</b>
</p>
<p>
    You were invited to {{ config('app.name') }}. Click <a href="{{ route('users.invitation.verify', $data['signature']) }}">HERE</a> to register.
</p>

</body>
</html>
