<?php

namespace Drewlabs\Passwords;

use Drewlabs\Passwords\Contracts\HashedTokenInterface;
use Drewlabs\Passwords\Contracts\TokenHasher;
use Drewlabs\Passwords\Contracts\TokenInterface;

class PasswordResetTokenHashManager implements TokenHasher
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
                return password_hash((string)$value, PASSWORD_BCRYPT, $options);
            }

            public function check($value, $hashed_value, array $options = []): bool
            {
                return (isset($hashed_value) || (!empty($hashed_value))) ? password_verify($value, $hashed_value) : false;
            }

            public function needsRehash(string $hash, array $options = [])
            {
                return password_needs_rehash($hash, PASSWORD_BCRYPT, $options ?? []);
            }
        };
    }

    public function make(TokenInterface $token): HashedTokenInterface
    {
        return new HashedPasswordResetToken($token->getSubject(), $this->hasher->make($token->getToken()), $token->getCreatedAt());
    }

    public function check(HashedTokenInterface $token, string $value): bool
    {
        return $this->hasher->check($value, $token->getToken());
    }
}
