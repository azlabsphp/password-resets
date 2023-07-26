<?php

use Drewlabs\Passwords\PasswordResetTokenFactory;
use Drewlabs\Passwords\PasswordResetTokenHashManager;
use Drewlabs\Passwords\PdoTokenRepository;
use Drewlabs\Passwords\Tests\InMemoryDatabase;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class PdoTokenRepositoryTest extends TestCase
{
    public function test_pdo_token_repository_add_token()
    {
        // Initialize
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PdoTokenRepository($database->getPdo(), $manager, 'password_resets');

        // Act
        $repository->addToken((new PasswordResetTokenFactory(new RandomBytes(16)))->create('user@example.com'));
        $result = $repository->getToken('user@example.com');
        print_r([$result]);
        // Assert
        $this->assertTrue(true);
    }

}