<?php

namespace App\Support\Commands;

use App\Contracts\CanResetPassword;
use App\Contracts\CanResetPasswordProvider;
use App\Contracts\TokenRepositoryInterface;
use App\Exceptions\PasswordResetTokenInvalidException;
use App\Exceptions\UserNotFoundException;
use App\Support\Events\ResetPassword;
use App\Support\OtpPasswordTokenFactory;
use Closure;

class OtpResetPasswordCommand
{
    /**
     * @var OtpPasswordTokenFactory
     */
    private $tokenFactory;

    /**
     * @var TokenRepositoryInterface
     */
    private $repository;

    /**
     * @var CanResetPasswordProvider
     */
    private $users;

    /**
     * @var callable
     */
    private $dispatcher;

    /**
     * Create command class instance
     * 
     * @param TokenRepositoryInterface $repository 
     * @param CanResetPasswordProvider $users 
     * @param callable $dispatcher 
     * @param OtpPasswordTokenFactory $tokenFactory 
     * @return void 
     */
    public function __construct(
        TokenRepositoryInterface $repository,
        CanResetPasswordProvider $users,
        callable $dispatcher,
        OtpPasswordTokenFactory $tokenFactory
    ) {
        $this->repository = $repository;
        $this->users = $users;
        $this->dispatcher = $dispatcher;
        $this->tokenFactory = $tokenFactory;
    }

    /**
     * handle reset password action
     * 
     * @param mixed $sub 
     * @param string|int $otp 
     * @param string $password 
     * @param Closure(Authenticatable $user, string $password) $callback 
     * @return mixed 
     * @throws UserNotFoundException 
     * @throws PasswordResetTokenInvalidException 
     */
    public function handle($sub, $otp, string $password, \Closure $callback)
    {
        if (null === ($user = $this->users->retrieveForPasswordReset((string)$sub))) {
            throw new UserNotFoundException($sub);
        }

        // Check if repository has a given token
        if ($this->repository->hasToken($sub, $token = $this->tokenFactory->create($sub, (string)$otp)->getToken())) {
            throw new PasswordResetTokenInvalidException($token);
        }

        $callback = $callback ?? function (CanResetPassword $user, string $password) {
            call_user_func($this->dispatcher, new ResetPassword($user, $password));
        };

        // Remove the subject token from repostory
        $this->repository->deleteToken($sub);

        // Call the callback instance on the user and password variables
        return call_user_func($callback, $user, $password);
    }
}
