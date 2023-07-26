<?php

use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Otp;
use Drewlabs\Passwords\OtpPasswordResetTokenFactory;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class OtpPasswordResetTokenFactoryTest extends TestCase
{
    public function test_otp_password_reset_token_factory_create()
    {
        // Initialize
        $factory = new OtpPasswordResetTokenFactory(new RandomBytes(16));

        // Assert
        $this->assertInstanceOf(TokenInterface::class, $factory->create('user@example.com', new Otp));
    }

}