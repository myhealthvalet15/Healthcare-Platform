<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $subject ?? 'Notification' }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; margin: 0;">
    <div style="background-color: #ffffff; padding: 20px; border-radius: 6px; max-width: 600px; margin: 0 auto;">

        {{-- 🔷 Header --}}
        <div style="text-align: center; margin-bottom: 20px;">
            <a href="https://login-users.hygeiaes.com" style="text-decoration: none; color: inherit;">
                <div style="display: inline-block;">
                    <svg width="32" height="20" viewBox="0 0 32 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M0.00172773 0V6.85398C0.00172773 6.85398 -0.133178 9.01207 1.98092 10.8388L13.6912 21.9964L19.7809 21.9181L18.8042 9.88248L16.4951 7.17289L9.23799 0H0.00172773Z" fill="#7367F0"/>
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M7.69824 16.4364L12.5199 3.23696L16.5541 7.25596L7.69824 16.4364Z" fill="#161616"/>
                        <path opacity="0.06" fill-rule="evenodd" clip-rule="evenodd" d="M8.07751 15.9175L13.9419 4.63989L16.5849 7.28475L8.07751 15.9175Z" fill="#161616"/>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.77295 16.3566L23.6563 0H32V6.88383C32 6.88383 31.8262 9.17836 30.6591 10.4057L19.7824 22H13.6938L7.77295 16.3566Z" fill="#7367F0"/>
                    </svg>
                    <div style="font-size: 18px; font-weight: bold; color: #333;">myHealthvalet</div>
                </div>
            </a>
        </div>

        {{-- 🔹 Divider after header --}}
        <hr style="border: none; border-top: 1px solid #ddd; margin: 10px 0 20px 0;"/>

        {{-- 🔷 Body --}}
        <h2 style="color: #007BFF; font-size: 20px; margin-bottom: 20px;">{{ $header_title ?? 'Notification' }}</h2>

        <p><strong>Event:</strong> {{ $event_name }}</p>
        <p><strong>Description:</strong> {{ $event_description }}</p>
        <p><strong>Guest:</strong> {{ $guest_name }}</p>
        <p><strong>From:</strong> {{ $from_date }}</p>
        <p><strong>To:</strong> {{ $to_date }}</p>
        <p><strong>Departments:</strong> {{ implode(', ', $department_names ?? []) }}</p>
        <p><strong>Employee Types:</strong> {{ implode(', ', $employee_type_names ?? []) }}</p>
{{-- 🔷 RSVP Buttons --}}
<div style="margin-top: 30px; text-align: center;">
    <p style="font-size: 16px;">Will you attend this event?</p>
    <a href="https://yourdomain.com/rsvp-response?event_id=10&user_id=MU326c902cc8&response=yes"
       style="background-color: #28a745; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px; margin-right: 10px;">
        ✅ Yes, I’m attending
    </a>
    <a href="https://yourdomain.com/rsvp-response?event_id=10&user_id=MU326c902cc8&response=no"
       style="background-color: #dc3545; color: #fff; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
        ❌ No, I’m not attending
    </a>
</div>


        {{-- 🔻 Footer Divider --}}
        <hr style="margin: 30px 0; border: none; border-top: 1px solid #eee;" />

        {{-- 🔻 Footer --}}
        <div style="font-size: 12px; color: #777; text-align: center;">
            <p>
                &copy; {{ date('Y') }} made with ❤️ by 
                <a href="https://pixinvent.com" style="color: #7367F0; text-decoration: none;" target="_blank">Pixinvent</a>
            </p>
            <p>
                <a href="https://themeforest.net/licenses/standard" style="margin: 0 8px; color: #007BFF; text-decoration: none;" target="_blank">License</a> |
                <a href="https://1.envato.market/pixinvent_portfolio" style="margin: 0 8px; color: #007BFF; text-decoration: none;" target="_blank">More Themes</a> |
                <a href="https://demos.pixinvent.com/vuexy-html-admin-template/documentation/laravel-introduction.html" style="margin: 0 8px; color: #007BFF; text-decoration: none;" target="_blank">Docs</a> |
                <a href="https://pixinvent.ticksy.com/" style="margin: 0 8px; color: #007BFF; text-decoration: none;" target="_blank">Support</a>
            </p>
        </div>

    </div>
</body>
</html>
