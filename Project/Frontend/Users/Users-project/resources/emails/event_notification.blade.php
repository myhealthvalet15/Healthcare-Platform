<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject }}</title>
</head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px;">
    <div style="background: #fff; padding: 20px; border-radius: 6px;">
        <h2 style="color: #007BFF;">{{ $header_title ?? 'Event Notification' }}</h2>
        <div>{!! $body !!}</div>
        <p style="margin-top: 30px; font-size: 12px; color: #777;">
            &copy; {{ date('Y') }} Your Company Name
        </p>
    </div>
</body>
</html>
