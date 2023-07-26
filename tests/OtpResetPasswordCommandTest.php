<?php

use Drewlabs\Passwords\Commands\CreatePasswordResetOtpCommand as CreatePasswordResetCommand;
use Drewlabs\Passwords\Commands\OtpResetPasswordCommand as ResetPasswordCommand;
use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Exceptions\PasswordResetTokenInvalidException;
use Drewlabs\Passwords\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\Otp;
use Drewlabs\Passwords\OtpPasswordResetTokenFactory;
use Drewlabs\Passwords\PasswordResetTokenFactory;
use Drewlabs\Passwords\PasswordResetTokenHashManager;
use Drewlabs\Passwords\PasswordResetTokenRepository;
use Drewlabs\Passwords\Tests\CanResetPasswordProvider;
use Drewlabs\Passwords\Tests\InMemoryDatabase;
use Drewlabs\Passwords\Tests\RandomBytes;
use Drewlabs\Passwords\Tests\TestUrlFactory;
use Drewlabs\Passwords\UrlFactory;
use PHPUnit\Framework\TestCase;

class OtpResetPasswordCommandTest extends TestCase
{

    public function setUp(): void
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection()->table('password_resets'), $manager);
        $repository->deleteToken('user@example.com');
    }

    public function test_reset_password_command_throw_user_not_found_exception_is_user_does_not_exists()
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection()->table('password_resets'), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider, new OtpPasswordResetTokenFactory);
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
        $repository = new PasswordResetTokenRepository($database->getConnection()->table('password_resets'), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider, new OtpPasswordResetTokenFactory);
        // Assert
        $this->expectException(PasswordResetTokenInvalidException::class);

        // Act
        $createCommand = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, new OtpPasswordResetTokenFactory);
        $createCommand->handle('user@example.com');
        $command->handle('user@example.com', new Otp, 'MyPassword');
    }

    public function test_reset_password_command_invoke_callback_with_user_and_password()
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection()->table('password_resets'), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider, new OtpPasswordResetTokenFactory);
        
        // Act
        /**
         * @var string
         */
        $otp = null;
        $password = null;
        $createCommand = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, new OtpPasswordResetTokenFactory);
        $callback = function(CanResetPassword $user, string $value) use (&$otp) {
            $otp = $value;
        };
        $createCommand->handle('user@example.com', $callback);
        $command->handle('user@example.com', $otp, 'MyPassword', function($user, $pass) use (&$password) {
            $password = $pass;
        });
        // Assert
        $this->assertEquals('MyPassword', $password);
    }

    
    public function test_reset_password_command_delete_user_token_on_success()
    {
        $manager =  new PasswordResetTokenHashManager;
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection()->table('password_resets'), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider, new OtpPasswordResetTokenFactory);
        
        // Act
        /**
         * @var string
         */
        $otp = null;
        $password = null;
        $createCommand = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider, new OtpPasswordResetTokenFactory);
        $callback = function(CanResetPassword $user, string $value) use (&$otp) {
            $otp = $value;
        };
        $createCommand->handle('user@example.com', $callback);
        $command->handle('user@example.com', $otp, 'MyPassword', function($user, $pass) use (&$password) {
            $password = $pass;
        });

        // Assert
        $this->assertNull($repository->getToken('user@example.com'));
    }

}