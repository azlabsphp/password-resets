<?php

use Drewlabs\Passwords\Commands\CreatePasswordResetCommand;
use Drewlabs\Passwords\Commands\ResetPasswordCommand;
use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Exceptions\PasswordResetTokenInvalidException;
use Drewlabs\Passwords\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\PasswordResetTokenHashManager;
use Drewlabs\Passwords\PasswordResetTokenRepository;
use Drewlabs\Passwords\Tests\CanResetPasswordProvider;
use Drewlabs\Passwords\Tests\InMemoryDatabase;
use Drewlabs\Passwords\Tests\RandomBytes;
use Drewlabs\Passwords\Tests\TestUrlFactory;
use Drewlabs\Passwords\UrlFactory;
use PHPUnit\Framework\TestCase;

class ResetPasswordCommandTest extends TestCase
{

    public function setUp(): void
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $repository->deleteToken('user@example.com');
    }

    public function test_reset_password_command_throw_user_not_found_exception_is_user_does_not_exists()
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider);
        // Assert
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage(sprintf("Cannot find user %s", 'test@example.com'));

        // Act
        $command->handle('test@example.com', 'MyToken', 'MyPassword');
    }

    public function test_reset_password_command_throws_invalid_password_reset_token_exception_if_token_is_not_valid()
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider);
        // Assert
        $this->expectException(PasswordResetTokenInvalidException::class);

        // Act
        $createCommand = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, new RandomBytes, new UrlFactory(new TestUrlFactory));
        $createCommand->handle('user@example.com', 'password.create');
        $command->handle('user@example.com', 'MyToken', 'MyPassword');
    }

    public function test_reset_password_command_invoke_callback_with_user_and_password()
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider);
        
        // Act
        /**
         * @var string
         */
        $passwordToken = null;
        $password = null;
        $createCommand = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, new RandomBytes, new UrlFactory(new TestUrlFactory));
        $callback = function(CanResetPassword $user, string $url, TokenInterface $token) use (&$passwordToken) {
            $passwordToken = $token->getToken();
        };
        $createCommand->handle('user@example.com', 'password.create', $callback);
        $command->handle('user@example.com', $passwordToken, 'MyPassword', function($user, $pass) use (&$password) {
            $password = $pass;
        });
        // Assert
        $this->assertEquals('MyPassword', $password);
    }

    
    public function test_reset_password_command_delete_user_token_on_success()
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider);
        
        // Act
        /**
         * @var string
         */
        $passwordToken = null;
        $createCommand = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, new RandomBytes, new UrlFactory(new TestUrlFactory));
        $callback = function(CanResetPassword $user, string $url, TokenInterface $token) use (&$passwordToken) {
            $passwordToken = $token->getToken();
        };
        $createCommand->handle('user@example.com', 'password.create', $callback);
        $command->handle('user@example.com', $passwordToken, 'MyPassword');

        // Assert
        $this->assertNull($repository->getToken('user@example.com'));
    }
}