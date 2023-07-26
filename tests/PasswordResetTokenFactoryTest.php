<?php

use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\PasswordResetTokenFactory;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenFactoryTest extends TestCase
{
    public function test_password_reset_token_factory_create_return_instance_of_token_interface()
    {
        $factory = new PasswordResetTokenFactory(new RandomBytes(16));
        $prToken = $factory->create('user@example.com');

        // Assert
        $this->assertInstanceOf(TokenInterface::class, $prToken);
    }

}