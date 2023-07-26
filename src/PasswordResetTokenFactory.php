<?php

namespace Drewlabs\Passwords;

use DateTimeImmutable;
use Drewlabs\Passwords\Contracts\TokenInterface;
use InvalidArgumentException;

class PasswordResetTokenFactory
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $algo;

    /**
     * Creates factory instance
     * 
     * @param string $key 
     * @param string $algo 
     * @return void 
     * @throws InvalidArgumentException 
     */
    public function __construct(string $key,  $algo = 'sha256')
    {
        if (0 === strpos($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }
        if (false === $key) {
            throw new InvalidArgumentException(sprintf("%s is not a valid secret key string", $key));
        }
        $this->key = $key;
        $this->algo = $algo ?? 'sha256';
    }

    /**
     * Creates password token instance
     * 
     * @param string $sub
     *  
     * @return TokenInterface 
     */
    public function create($sub): TokenInterface
    {
        $token = hash_hmac($this->algo ?? 'sha256', $this->newKey(40), $this->key);

        // return token static instance
        return new PasswordResetToken((string)$sub, $token, new DateTimeImmutable);
    }

    /**
     * create new random base64 encoded bytes
     * 
     * @param int $bytes 
     * @return string 
     */
    private function newKey(int $bytes)
    {
        return str_replace('=', '', str_replace([\chr(92), '+', \chr(47), \chr(38)], '.', base64_encode(openssl_random_pseudo_bytes($bytes))));
    }
}
