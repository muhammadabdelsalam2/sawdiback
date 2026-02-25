<?php
namespace App\Services\API\Auth\Contracts;

use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

interface OtpSenderInterface
{
    public function send(string $identifier, string $code): void;
}

class EmailOtpSender implements OtpSenderInterface
{
    public function send(string $identifier, string $code): void
    {
        Mail::to($identifier)->send(new OtpMail($code,$identifier));
    }
}

class PhoneOtpSender implements OtpSenderInterface
{
    public function send(string $identifier, string $code): void
    {
        // Example: Use SMS service
        // \App\Services\SmsService::send($identifier, "Your OTP code is: $code");
    }
}

class OtpSenderFactory
{
    public static function make(string $identifier): OtpSenderInterface
    {
        return filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? new EmailOtpSender()
            : new PhoneOtpSender();
    }
}