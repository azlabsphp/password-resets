<?php

use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\PasswordResetToken;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenTest extends TestCase
{

    public function test_password_reset_token_constructor()
    {
        $passwordResetToken = new PasswordResetToken('user@example.com', new RandomBytes, new DateTimeImmutable);

        // Assert
        $this->assertInstanceOf(TokenInterface::class, $passwordResetToken);
    }

    public function test_password_reset_token_get_subject()
    {
        $passwordResetToken = new PasswordResetToken('user@example.com', new RandomBytes, new DateTimeImmutable);

        // Assert
        $this->assertEquals('user@example.com', $passwordResetToken->getSubject());
    }

    public function test_password_reset_token_get_created_at()
    {
        $createdAt = new DateTimeImmutable;
        $passwordResetToken = new PasswordResetToken('user@example.com', new RandomBytes, $createdAt);

        // Assert
        $this->assertEquals($createdAt, $passwordResetToken->getCreatedAt());
    }

    public function test_password_reset_token_get_token()
    {
        $bytes = new RandomBytes;
        $passwordResetToken = new PasswordResetToken('user@example.com', $bytes, new DateTimeImmutable);

        // Assert
        $this->assertEquals((string)$bytes, $passwordResetToken->getToken());
    }
}