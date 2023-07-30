<?php

declare(strict_types=1);

/*
 * This file is part of the drewlabs namespace.
 *
 * (c) Sidoine Azandrew <azandrewdevelopper@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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
        $token = (new PasswordResetTokenFactory((string)(new RandomBytes(16))))->create('user@example.com');

        // Act
        $hashedValue = $manager->make($token);

        // Assert
        $this->assertTrue(password_verify($token->getToken(), $hashedValue->getToken()));
    }

    public function test_password_reset_token_hash_Manager_make_check_return_true_for_same_otp_string()
    {
        // Initialize
        $manager = new PasswordResetTokenHashManager();
        $otp = (string)(new Otp);
        $token = (new OtpPasswordResetTokenFactory((string)(new RandomBytes(16))))->create('user@example.com', $otp);

        // Act
        $hashedToken = $manager->make($token);
        $newToken = (new OtpPasswordResetTokenFactory((string)(new RandomBytes(16))))->create('user@example.com', $otp);

        // Assert
        $this->assertTrue($manager->check($hashedToken, $newToken->getToken()));
    }

    public function test_password_reset_token_hash_Manager_make_check_return_true_for_same_password_reset_token_string()
    {
        // Initialize
        $manager = new PasswordResetTokenHashManager();
        $token = (new PasswordResetTokenFactory((string)(new RandomBytes(16))))->create('user@example.com');

        // Act
        $hashedToken = $manager->make($token);

        // Assert
        $this->assertTrue($manager->check($hashedToken, $token->getToken()));
    }
}
