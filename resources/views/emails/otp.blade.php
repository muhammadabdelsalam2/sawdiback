@component('mail::message', ['logo' => $logo])
{{-- Logo --}}
<div style="text-align: center; margin-bottom: 20px;">
    <img src="{{ $logo }}" alt="{{ config('app.name') }}" style="max-width: 150px; width: 100%; height: auto;">
</div>

# {{ __('mail.hello') }}

{{ __('mail.otp_request', ['type' => ucfirst(__($type))]) }}

@component('mail::panel')
# {{ $code }}
@endcomponent

{{ __('mail.otp_expire') }}

{{ __('mail.ignore_notice') }}

{{ __('mail.thanks') }}<br>
{{ config('app.name') }}
@endcomponent
