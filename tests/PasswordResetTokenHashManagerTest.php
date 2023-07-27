<?php

use Drewlabs\Passwords\Otp;
use Drewlabs\Passwords\OtpPasswordResetTokenFactory;
use Drewlabs\Passwords\PasswordResetTokenFactory;
use Drewlabs\Passwords\PasswordResetTokenHashManager;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenHashManagerTest extends TestCase
{

    public function test_password_reset_token_hash_Manager_make_create_an_bcrypt_hash_by_default()
    {
        // Initialize
        $manager = new PasswordResetTokenHashManager();
        $token = (new PasswordResetTokenFactory(new RandomBytes(16)))->create('user@example.com');

        // Act
        $hashedValue = $manager->make($token);

        // Assert
        $this->assertTrue(password_verify($token->getToken(), $hashedValue->getToken()));
    }

    public function test_password_reset_token_hash_Manager_make_check_return_true_for_same_otp_string()
    {
        // Initialize
        $manager = new PasswordResetTokenHashManager();
        $otp = new Otp;
        $token = (new OtpPasswordResetTokenFactory(new RandomBytes(16)))->create('user@example.com', $otp);

        // Act
        $hashedToken = $manager->make($token);
        $newToken = (new OtpPasswordResetTokenFactory(new RandomBytes(16)))->create('user@example.com', $otp);

        // Assert
        $this->assertTrue($manager->check($hashedToken, $newToken->getToken()));
    }

    public function test_password_reset_token_hash_Manager_make_check_return_true_for_same_password_reset_token_string()
    {
        // Initialize
        $manager = new PasswordResetTokenHashManager();
        $token = (new PasswordResetTokenFactory(new RandomBytes(16)))->create('user@example.com');

        // Act
        $hashedToken = $manager->make($token);

        // Assert
        $this->assertTrue($manager->check($hashedToken, $token->getToken()));
    }
}