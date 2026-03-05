<?php

namespace App\Enums;

enum OtpType: string
{
    //
    public const LOGIN = 'login';
    public const REGISTER = 'register';
    public const FORGOT_PASSWORD = 'forgot_password';
    public const RESET_PASSWORD = 'reset_password';
    public const RESEND = 'resend';
    public const VERIFY_PHONE = 'verify_phone';  
    public const VERIFY_EMAIL = 'verify_email';


}
