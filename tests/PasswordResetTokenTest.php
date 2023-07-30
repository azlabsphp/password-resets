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
use Drewlabs\Passwords\PasswordResetToken;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenTest extends TestCase
{
    public function test_password_reset_token_constructor()
    {
        $passwordResetToken = new PasswordResetToken('user@example.com', (string)(new RandomBytes()), new DateTimeImmutable());

        // Assert
        $this->assertInstanceOf(TokenInterface::class, $passwordResetToken);
    }

    public function test_password_reset_token_get_subject()
    {
        $passwordResetToken = new PasswordResetToken('user@example.com', (string)(new RandomBytes()), new DateTimeImmutable());

        // Assert
        $this->assertSame('user@example.com', $passwordResetToken->getSubject());
    }

    public function test_password_reset_token_get_created_at()
    {
        $createdAt = new DateTimeImmutable();
        $passwordResetToken = new PasswordResetToken('user@example.com', (string)(new RandomBytes()), $createdAt);

        // Assert
        $this->assertSame($createdAt, $passwordResetToken->getCreatedAt());
    }

    public function test_password_reset_token_get_token()
    {
        $bytes = new RandomBytes();
        $passwordResetToken = new PasswordResetToken('user@example.com', (string)$bytes, new DateTimeImmutable());

        // Assert
        $this->assertSame((string) $bytes, $passwordResetToken->getToken());
    }
}
