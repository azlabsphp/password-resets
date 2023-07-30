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

use Drewlabs\Passwords\Commands\CreatePasswordResetOtpCommand as CreatePasswordResetCommand;
use Drewlabs\Passwords\Commands\OtpResetPasswordCommand as ResetPasswordCommand;
use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Exceptions\PasswordResetTokenInvalidException;
use Drewlabs\Passwords\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\Otp;
use Drewlabs\Passwords\PasswordResetTokenHashManager;
use Drewlabs\Passwords\PasswordResetTokenRepository;
use Drewlabs\Passwords\Tests\CanResetPasswordProvider;
use Drewlabs\Passwords\Tests\InMemoryDatabase;
use PHPUnit\Framework\TestCase;

class OtpResetPasswordCommandTest extends TestCase
{
    protected function setUp(): void
    {
        $manager = new PasswordResetTokenHashManager();
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $repository->deleteToken('user@example.com');
    }

    public function test_reset_password_command_throw_user_not_found_exception_is_user_does_not_exists()
    {
        $manager = new PasswordResetTokenHashManager();
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider());
        // Assert
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Cannot find user %s', 'test@example.com'));

        // Act
        $command->handle('test@example.com', 'MyToken', 'MyPassword');
    }

    public function test_reset_password_command_throws_invalid_password_reset_token_exception_if_token_is_not_valid()
    {
        $manager = new PasswordResetTokenHashManager();
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider());
        // Assert
        $this->expectException(PasswordResetTokenInvalidException::class);

        // Act
        $createCommand = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider());
        $createCommand->handle('user@example.com');
        $command->handle('user@example.com', (string)new Otp(), 'MyPassword');
    }

    public function test_reset_password_command_invoke_callback_with_user_and_password()
    {
        $manager = new PasswordResetTokenHashManager();
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider());

        // Act
        /**
         * @var string
         */
        $otp = null;
        $password = null;
        $createCommand = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider());
        $callback = static function (CanResetPassword $user, string $value) use (&$otp) {
            $otp = $value;
        };
        $createCommand->handle('user@example.com', $callback);
        $command->handle('user@example.com', $otp, 'MyPassword', static function ($user, $pass) use (&$password) {
            $password = $pass;
        });
        // Assert
        $this->assertSame('MyPassword', $password);
    }

    public function test_reset_password_command_delete_user_token_on_success()
    {
        $manager = new PasswordResetTokenHashManager();
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new ResetPasswordCommand($repository, new CanResetPasswordProvider());

        // Act
        /**
         * @var string
         */
        $otp = null;
        $password = null;
        $createCommand = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider());
        $callback = static function (CanResetPassword $user, string $value) use (&$otp) {
            $otp = $value;
        };
        $createCommand->handle('user@example.com', $callback);
        $command->handle('user@example.com', $otp, 'MyPassword', static function ($user, $pass) use (&$password) {
            $password = $pass;
        });

        // Assert
        $this->assertNull($repository->getToken('user@example.com'));
    }
}
