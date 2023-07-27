<?php

namespace Drewlabs\Passwords\Events;

use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\TokenInterface;

class PasswordResetLinkCreated
{
    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var CanResetPassword
     */
    private $user;

    /**
     * Creates password reset link created event instance
     * 
     * 
     * @param CanResetPassword $user 
     * @param TokenInterface $token 
     */
    public function __construct(CanResetPassword $user, TokenInterface $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getUser()
    {
        return $this->user;
    }
}
