<?php

namespace Drewlabs\Passwords;

use Drewlabs\Passwords\Traits\PasswordToken as PasswordTokenMixin;
use DateTimeInterface;
use Drewlabs\Passwords\Contracts\TokenInterface;

class PasswordResetToken implements TokenInterface
{
    use PasswordTokenMixin;

    /**
     * Creates password token class instance
     * 
     * @param mixed $sub 
     * @param string $token 
     * @param DateTimeInterface $createdAt 
     * @param DateTimeInterface|null $expiresAt 
     */
    public function __construct($sub, string $token, \DateTimeInterface $createdAt, \DateTimeInterface $expiresAt = null)
    {
        $this->token = $token;
        $this->createdAt = $createdAt;
        $this->sub = $sub;
        $this->expiresAt = $expiresAt;
    }
}
