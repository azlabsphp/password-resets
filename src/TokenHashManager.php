<?php

namespace App\Support;

use App\Contracts\HashedTokenInterface;
use App\Contracts\TokenHasher;
use App\Contracts\TokenInterface;

class TokenHashManager implements TokenHasher
{
    /**
     * @var IHasher
     */
    private $hasher;

    /**
     * Creates token hash manager class instance
     * 
     * @param \Drewlabs\Contracts\Hasher\IHasher|null $hasher 
     */
    public function __construct($hasher = null)
    {
        // Use md5 hasher if not hasher is provided
        $this->hasher = $hasher ?? new class
        {
            public function make($value, array $options = [])
            {
                return md5((string)$value);
            }

            public function check($value, $hashed_value, array $options = []): bool
            {
                return strcmp(md5($value), $hashed_value);
            }
        };
    }

    public function hash(TokenInterface $token): HashedTokenInterface
    {
        return new HashedPasswordToken($token->getSubject(), $this->hasher->make($token->getToken()), $token->getCreatedAt());
    }

    public function check(HashedTokenInterface $token, string $value): bool
    {
        return $this->hasher->check($value, $token->getToken());
    }
}
