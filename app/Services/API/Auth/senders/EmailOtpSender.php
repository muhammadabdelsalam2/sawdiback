<?php
namespace App\Services\API\Auth\Senders;

use App\Services\API\Auth\Contracts\OtpSenderInterface;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\Mail;

class EmailOtpSender implements OtpSenderInterface
{
    public function send(string $identifier, string $code, string $type = 'login'): void
    {
        Mail::to($identifier)->send(new OtpMail($code, $identifier, $type));
    }
}