@component('mail::message')
# Hello!

Your OTP code for **{{ ucfirst($type) }}** is:

@component('mail::panel')
# {{ $code }}
@endcomponent

This code will expire in **10 minutes**.

If you did not request this, please ignore this email.

Thanks,<br>
{{ config('app.name') }}
@endcomponent