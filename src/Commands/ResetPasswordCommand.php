<?php

namespace Drewlabs\Passwords\Commands;


use App\Exceptions\PasswordResetTokenInvalidException;
use App\Exceptions\UserNotFoundException;
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
     * Create command class instance
     * 
     * @param TokenRepositoryInterface $repository 
     * @param CanResetPasswordProvider $users 
     * @param callable $dispatcher 
     * @return void 
     */
    public function __construct(
        TokenRepositoryInterface $repository,
        CanResetPasswordProvider $users,
        callable $dispatcher
    ) {
        $this->repository = $repository;
        $this->users = $users;
        $this->dispatcher = $dispatcher;
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
    public function handle($sub, string $token, string $password, \Closure $callback)
    {
        if (null === ($user = $this->users->retrieveForPasswordReset((string)$sub))) {
            throw new UserNotFoundException($sub);
        }

        // Check if repository has a given token
        if ($this->repository->hasToken($sub, $token)) {
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
