<?php

use Drewlabs\Passwords\Commands\CreatePasswordResetCommand;
use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Exceptions\ThrottleResetException;
use Drewlabs\Passwords\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\PasswordResetTokenFactory;
use Drewlabs\Passwords\PasswordResetTokenHashManager;
use Drewlabs\Passwords\PasswordResetTokenRepository;
use Drewlabs\Passwords\Tests\CanResetPasswordProvider;
use Drewlabs\Passwords\Tests\InMemoryDatabase;
use Drewlabs\Passwords\Tests\RandomBytes;
use Drewlabs\Passwords\Tests\TestUrlFactory;
use Drewlabs\Passwords\UrlFactory;
use PHPUnit\Framework\TestCase;

class CreatePasswordResetCommandTest extends TestCase
{
    public function setUp(): void
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection()->table('password_resets'), $manager);
        $repository->deleteToken('user@example.com');
    }

    public function test_create_password_reset_command_handle_throw_user_not_found_exception_is_user_is_not_found()
    {
        // Initialize
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection()->table('password_resets'), $manager);
        $command = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, new PasswordResetTokenFactory(new RandomBytes), new UrlFactory(new TestUrlFactory));

        // Assert
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage(sprintf("Cannot find user %s", 'test@example.com'));

        // Act
        $command->handle('test@example.com', 'password.create');
    }

    
    public function test_create_password_reset_command_handle_throw_user_throttle_request_exception_on_many_calls()
    {
        // Initialize
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection()->table('password_resets'), $manager);
        $command = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, new PasswordResetTokenFactory(new RandomBytes), new UrlFactory(new TestUrlFactory));

        // Assert
        $this->expectException(ThrottleResetException::class);
        $this->expectExceptionMessage(sprintf("Too many attempts for %s", 'user@example.com'));

        // Act
        $command->handle('user@example.com', 'password.create');
        $command->handle('user@example.com', 'password.create');
    }

    public function test_create_password_reset_command_handle_call_callback_on_success()
    {
        // Initialize
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection()->table('password_resets'), $manager);
        $command = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, new PasswordResetTokenFactory(new RandomBytes), new UrlFactory(new TestUrlFactory));

        $totalCalls = 0;
        $calledWith = null;
        $passwordToken = null;

        // Act
        $callback = function(CanResetPassword $user, string $url, TokenInterface $token) use (&$totalCalls, &$calledWith, &$passwordToken) {
            $totalCalls++;
            $calledWith = $url;
            $passwordToken = $token->getToken();
        };
        $command->handle('user@example.com', 'password.create', $callback);

        // Assert
        $this->assertEquals(1, $totalCalls);
        $this->assertEquals('https://localhost:8000/api/v1/examples?token=' . $passwordToken, $calledWith);
    }
}