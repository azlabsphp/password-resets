<?php

namespace App\Support\Events;

use App\Contracts\CanResetPassword;

class PasswordResetLinkCreated
{
    /**
     * @var string
     */
    private $url;

    /**
     * @var CanResetPassword
     */
    private $user;

    /**
     * Creates password reset link created event instance
     * 
     * 
     * @param CanResetPassword $user 
     * @param string $url 
     * @return void 
     */
    public function __construct(CanResetPassword $user, string $url)
    {
        $this->user = $user;
        $this->url = $url;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getUser()
    {
        return $this->user;
    }
}
