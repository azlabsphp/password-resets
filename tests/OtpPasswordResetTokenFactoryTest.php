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
        $this->assertInstanceOf(TokenInterface::class, $factory->create('user@example.com', (string)(new Otp)));
    }
}
