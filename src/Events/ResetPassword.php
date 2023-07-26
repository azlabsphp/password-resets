<?php

namespace Drewlabs\Passwords\Events;

use Drewlabs\Passwords\Contracts\CanResetPassword;

class ResetPassword
{
    /**
     * @var string
     */
    private $password;

    /**
     * @var CanResetPassword
     */
    private $user;

    /**
     * Create event class instance
     * 
     * @param CanResetPassword $user 
     * @param string $password 
     */
    public function __construct(CanResetPassword $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
