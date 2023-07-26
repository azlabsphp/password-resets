<?php

use Drewlabs\Passwords\Otp;
use PHPUnit\Framework\TestCase;

class OtpTest extends TestCase
{
    public function test_otp_to_string_return_numeric()
    {
        $otp = new Otp;
        $this->assertTrue(is_numeric((string) $otp));
    }

    public function test_otp_to_string()
    {
        $otp = new Otp;
        $this->assertTrue(is_string((string)$otp));
    }
}