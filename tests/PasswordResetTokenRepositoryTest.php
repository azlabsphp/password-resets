<?php

use Drewlabs\Passwords\PasswordResetToken;
use Drewlabs\Passwords\PasswordResetTokenFactory;
use Drewlabs\Passwords\PasswordResetTokenHashManager;
use Drewlabs\Passwords\PasswordResetTokenRepository;
use Drewlabs\Passwords\Tests\InMemoryDatabase;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenRepositoryTest extends TestCase
{
    public function test_pdo_token_repository_add_token()
    {
        // Initialize
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);

        // Act
        $repository->addToken((new PasswordResetTokenFactory(new RandomBytes(16)))->create('user@example.com'));
        $result = $repository->getToken('user@example.com');

        // Assert
        $this->assertEquals('user@example.com', $result->getSubject());
        $repository->deleteToken('user@example.com');
    }

    public function test_password_reset_token_repository_has_token_returns_false_if_token_expires()
    {
        // Initialize
        $createdAt = (new DateTimeImmutable)->modify("-2 hours");
        $randomBytes = new RandomBytes();
        $passwordResetToken = new PasswordResetToken('user@example.com', $randomBytes, $createdAt);
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);

        // Act
        $repository->addToken($passwordResetToken);

        // Assert
        $this->assertFalse($repository->hasToken('user@example.com', $passwordResetToken->getToken()));
    }

    public function test_password_reset_token_repository_has_token_returns_false_if_token_is_different()
    {
        // Initialize
        $createdAt = (new DateTimeImmutable);
        $randomBytes = new RandomBytes();
        $passwordResetToken = new PasswordResetToken('user@example.com', $randomBytes, $createdAt);
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);

        // Act
        $repository->addToken($passwordResetToken);

        // Assert
        $this->assertFalse($repository->hasToken('user@example.com', (string)(new RandomBytes)));
    }

    
    public function test_pdo_token_repository_delete_token_removes_subject_token_from_repository()
    {
        // Initialize
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);

        // Act
        $repository->addToken((new PasswordResetTokenFactory(new RandomBytes(16)))->create('user@example.com'));
        
        $repository->deleteToken('user@example.com');
        $result = $repository->getToken('user@example.com');

        // Assert
        $this->assertNull($result);
    }

}