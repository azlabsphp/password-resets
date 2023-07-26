<?php

namespace Drewlabs\Passwords;

use TypeError;
use Error;
use Exception;

class Otp
{
    /**
     * @var int
     */
    private $value;

    /**
     * Creates otp instance
     * 
     * @throws TypeError 
     * @throws Error 
     * @throws Exception 
     */
    public function __construct()
    {
        $this->value = random_int(100000, 999999);
    }

    /**
     * Returns the otp instance as string
     * 
     * @return string 
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}