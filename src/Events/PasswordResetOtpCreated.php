<?php

namespace Drewlabs\Passwords\Events;

use Drewlabs\Passwords\Contracts\CanResetPassword;

class PasswordResetOtpCreated
{
    /**
     * @var string
     */
    private $otp;

    /**
     * @var CanResetPassword
     */
    private $user;

    /**
     * Creates password reset otp created event instance
     * 
     * @param CanResetPassword $user 
     * @param string $url 
     * @return void 
     */
    public function __construct(CanResetPassword $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function getOTP()
    {
        return $this->otp;
    }

    public function getUser()
    {
        return $this->user;
    }
}
