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

use Drewlabs\Passwords\Contracts\HashedTokenInterface;
use Drewlabs\Passwords\HashedPasswordResetToken;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class HashedPasswordResetTokenTest extends TestCase
{
    public function test_hashed_password_token_contructor()
    {
        // Initialize
        $hashed = new HashedPasswordResetToken('admin@example.com', (string)(new RandomBytes()), new DateTimeImmutable());

        // Assert
        $this->assertInstanceOf(HashedTokenInterface::class, $hashed);
    }

    public function test_hashed_password_reset_token_getters()
    {
        // Initialize
        $sub = 'admin@example.com';
        $bytes = new RandomBytes();
        $createdAt = new DateTimeImmutable();
        $hashedPassword = new HashedPasswordResetToken($sub, (string)$bytes, $createdAt);

        // Assert
        $this->assertSame($createdAt, $hashedPassword->getCreatedAt());
        $this->assertSame((string) $bytes, $hashedPassword->getToken());
        $this->assertSame((string) $sub, $hashedPassword->getSubject());
    }

    public function test_hashed_password_reset_token_to_string()
    {
        // Initialize
        $sub = 'admin@example.com';
        $bytes = new RandomBytes();
        $createdAt = new DateTimeImmutable();
        $hashedPassword = new HashedPasswordResetToken($sub, (string)$bytes, $createdAt);

        // Assert
        $this->assertSame((string) $bytes, (string) $hashedPassword);
    }

    public function test_password_reset_token_with_expires_at_is_immutable()
    {
        $prToken = new HashedPasswordResetToken('user@example.com', (string)(new RandomBytes()), new DateTimeImmutable());

        $expiresAt = (new DateTimeImmutable())->modify('+30 minutes');
        $prToken2 = $prToken->withExpiresAt($expiresAt);

        $this->assertNotSame($prToken->getExpiresAt(), $prToken2->getExpiresAt());
        $this->assertNull($prToken->getExpiresAt());
        $this->assertSame($expiresAt, $prToken2->getExpiresAt());
    }
}
