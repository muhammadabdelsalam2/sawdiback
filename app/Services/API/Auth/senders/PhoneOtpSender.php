<?php
namespace App\Services\API\Auth\Senders;

use App\Services\API\Auth\Contracts\OtpSenderInterface;

class PhoneOtpSender implements OtpSenderInterface
{
    public function send(string $identifier, string $code, string $type = 'login'): void
    {
        // Replace this with your SMS service call
        // Example: \App\Services\SmsService::send($identifier, "Your OTP code: $code");
    }
}