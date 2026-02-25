<?php
namespace App\Services\API\Auth\Factories;

use App\Services\API\Auth\Contracts\OtpSenderInterface;
use App\Services\API\Auth\Senders\EmailOtpSender;
use App\Services\API\Auth\Senders\PhoneOtpSender;

class OtpSenderFactory
{
    public static function make(string $identifier): OtpSenderInterface
    {
        return filter_var($identifier, FILTER_VALIDATE_EMAIL)
            ? new EmailOtpSender()
            : new PhoneOtpSender();
    }
}