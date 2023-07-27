<?php

namespace Drewlabs\Passwords\Commands;


use Drewlabs\Passwords\Exceptions\PasswordResetTokenInvalidException;
use Drewlabs\Passwords\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\Events\ResetPassword;
use Closure;
use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\CanResetPasswordProvider;
use Drewlabs\Passwords\Contracts\TokenRepositoryInterface;

class ResetPasswordCommand
{

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
     * @var bool
     */
    private $autoReset = false;

    /**
     * Create command class instance
     * 
     * @param TokenRepositoryInterface $repository 
     * @param CanResetPasswordProvider $users 
     * @param callable|null $dispatcher
     * @param bool $autoReset
     * @return void 
     */
    public function __construct(
        TokenRepositoryInterface $repository,
        CanResetPasswordProvider $users,
        callable $dispatcher = null,
        bool $autoReset = false
    ) {
        $this->repository = $repository;
        $this->users = $users;
        $this->dispatcher = $dispatcher;
        $this->autoReset = $autoReset;
    }

    /**
     * handle reset password action
     * 
     * @param mixed $sub 
     * @param string $token 
     * @param string $password 
     * @param Closure(Authenticatable $user, string $password) $callback 
     * @return mixed 
     * @throws UserNotFoundException 
     * @throws PasswordResetTokenInvalidException 
     */
    public function handle($sub, string $token, string $password, \Closure $callback  = null)
    {
        if (null === ($user = $this->users->retrieveForPasswordReset((string)$sub))) {
            throw new UserNotFoundException($sub);
        }

        // Check if repository has a given token
        // If repository does not have the given token for the subject, we throw a PasswordResetTokenInvalidException
        if (!$this->repository->hasToken($sub, $token)) {
            throw new PasswordResetTokenInvalidException($token);
        }

        $callback = $callback ?? function (CanResetPassword $user, string $password) {
            if ($this->dispatcher) {
                call_user_func($this->dispatcher, new ResetPassword($user, $password));
            }
        };

        // Remove the subject token from repostory
        $this->repository->deleteToken($sub);

        // Case the library is configure to auto reset password, we call the reset password
        // method on the can reset password instance
        if ($this->autoReset) {
            $user->resetPassword($password);
        }

        // Call the callback instance on the user and password variables
        return call_user_func($callback, $user, $password);
    }
}
