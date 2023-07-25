<?php

namespace App\Support;

use DateTimeImmutable;
use InvalidArgumentException;
use App\Contracts\TokenInterface;

class OtpPasswordTokenFactory
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
    public function create($sub, string $otp): TokenInterface
    {
        $token = hash_hmac($this->algo ?? 'sha256', sprintf("otp_pass_token(%s)", (string)$otp), $this->key);

        // return token static instance
        return new PasswordToken($sub, $token, new DateTimeImmutable);
    }
}
