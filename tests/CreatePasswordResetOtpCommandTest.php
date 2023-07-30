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
use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Exceptions\ThrottleResetException;
use Drewlabs\Passwords\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\PasswordResetTokenHashManager;
use Drewlabs\Passwords\PasswordResetTokenRepository;
use Drewlabs\Passwords\Tests\CanResetPasswordProvider;
use Drewlabs\Passwords\Tests\InMemoryDatabase;
use PHPUnit\Framework\TestCase;

class CreatePasswordResetOtpCommandTest extends TestCase
{
    protected function setUp(): void
    {
        $manager = new PasswordResetTokenHashManager();
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $repository->deleteToken('user@example.com');
    }

    public function test_create_password_reset_otp_command_handle_throw_user_not_found_exception_is_user_is_not_found()
    {
        // Initialize
        $manager = new PasswordResetTokenHashManager();
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider());

        // Assert
        $this->expectException(UserNotFoundException::class);
        $this->expectExceptionMessage(sprintf('Cannot find user %s', 'test@example.com'));

        // Act
        $command->handle('test@example.com');
    }

    public function test_create_password_reset_otp_command_handle_throw_user_throttle_request_exception_on_many_calls()
    {
        // Initialize
        $manager = new PasswordResetTokenHashManager();
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider());

        // Assert
        $this->expectException(ThrottleResetException::class);
        $this->expectExceptionMessage(sprintf('Too many attempts for %s', 'user@example.com'));

        // Act
        $command->handle('user@example.com');
        $command->handle('user@example.com');
    }

    public function test_create_password_reset_otp_command_handle_call_callback_on_success()
    {
        // Initialize
        $manager = new PasswordResetTokenHashManager();
        $database = new InMemoryDatabase();
        $repository = new PasswordResetTokenRepository($database->getConnection(), $manager);
        $command = new CreatePasswordResetCommand($repository, new CanResetPasswordProvider());

        $totalCalls = 0;
        $calledWith = null;

        // Act
        $callback = static function (CanResetPassword $user, string $otp) use (&$totalCalls, &$calledWith) {
            ++$totalCalls;
            $calledWith = $otp;
        };
        $command->handle('user@example.com', $callback);

        // Assert
        $this->assertSame(1, $totalCalls);
        $is_numeric = is_numeric($calledWith);
        $this->assertTrue($is_numeric);
    }
}
