<?php 
namespace VanguardLTE\Http\Middleware
{
    class VerifyCsrfToken extends \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken
    {
        protected $except = [
            '/game/*/server', 
            '/payment/gigadat/*',
            '/coinpayment/ipn',
            '/phone_verify',
            '/phone_confirm',
            '/callback_cryptopayment'
        ];
    }
}
