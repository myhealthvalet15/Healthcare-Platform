<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptchaHelper
{
    public static function getCaptchaType()
    {
        return env('CAPTCHA_DRIVER', 'text');
    }

    // public static function renderCaptcha()
    // {
    //     $captchaType = self::getCaptchaType();
    //     if ($captchaType === 'google_v3') {
    //         return \NoCaptcha::renderJs() . \NoCaptcha::display();
    //     } elseif ($captchaType === 'text') {
    //         return captcha_img();
    //     }
    // }
}
