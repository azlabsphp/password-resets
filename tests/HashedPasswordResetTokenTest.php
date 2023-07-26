<?php

use Drewlabs\Passwords\Contracts\HashedTokenInterface;
use Drewlabs\Passwords\HashedPasswordResetToken;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class HashedPasswordResetTokenTest extends TestCase
{
    public function test_hashed_password_token_contructor()
    {
        // Initialize
        $hashed = new HashedPasswordResetToken('admin@example.com', new RandomBytes, new DateTimeImmutable);

        // Assert
        $this->assertInstanceOf(HashedTokenInterface::class, $hashed);
    }

    public function test_hashed_password_reset_token_getters()
    {
        // Initialize
        $sub = 'admin@example.com';
        $bytes = new RandomBytes;
        $createdAt = new DateTimeImmutable;
        $hashedPassword = new HashedPasswordResetToken($sub, $bytes, $createdAt);

        // Assert
        $this->assertEquals($createdAt, $hashedPassword->getCreatedAt());
        $this->assertEquals((string)$bytes, $hashedPassword->getToken());
        $this->assertEquals((string)$sub, $hashedPassword->getSubject());
    }

    public function test_hashed_password_reset_token_to_string()
    {
        // Initialize
        $sub = 'admin@example.com';
        $bytes = new RandomBytes;
        $createdAt = new DateTimeImmutable;
        $hashedPassword = new HashedPasswordResetToken($sub, $bytes, $createdAt);

        // Assert
        $this->assertEquals((string)$bytes, (string)$hashedPassword);
    }

}