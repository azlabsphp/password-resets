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
use Drewlabs\Passwords\PasswordResetTokenFactory;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenFactoryTest extends TestCase
{
    public function test_password_reset_token_factory_create_return_instance_of_token_interface()
    {
        $factory = new PasswordResetTokenFactory((string)(new RandomBytes(16)));
        $prToken = $factory->create('user@example.com');

        // Assert
        $this->assertInstanceOf(TokenInterface::class, $prToken);
    }
}
