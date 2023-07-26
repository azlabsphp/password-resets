<?php

namespace Drewlabs\Passwords;

use DateTimeImmutable;
use Drewlabs\Passwords\Contracts\TokenInterface;
use InvalidArgumentException;

class OtpPasswordResetTokenFactory
{

    /**
     * Creates factory instance
     * 
     * @param string $key 
     * @param string $algo 
     * @return void 
     * @throws InvalidArgumentException 
     */
    public function __construct()
    {
    }

    /**
     * Creates password token instance
     * 
     * @param string $sub
     *  
     * @return TokenInterface 
     */
    public function create($sub, string $otp): TokenInterface
    {
        $token = $this->base64UrlEncode(sprintf("otp_pass_token(%s, %s)", (string)$sub, (string)$otp));

        // return token static instance
        return new PasswordResetToken($sub, $token, new DateTimeImmutable);
    }

    /**
     * Return a base64 url encoded string
     * 
     * @param string $string 
     * @return string 
     */
    private function base64UrlEncode(string $string)
    {
        return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
    }
}
