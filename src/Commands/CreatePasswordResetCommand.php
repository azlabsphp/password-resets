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

namespace Drewlabs\Passwords\Commands;

use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\CanResetPasswordProvider;
use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Contracts\TokenRepositoryInterface;
use Drewlabs\Passwords\Events\PasswordResetLinkCreated;
use Drewlabs\Passwords\Exceptions\ThrottleResetException;
use Drewlabs\Passwords\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\PasswordResetTokenFactory;
use Drewlabs\Passwords\Traits\SupportThrottleRequests;

class CreatePasswordResetCommand
{
    use SupportThrottleRequests;

    /**
     * @var PasswordResetTokenFactory
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
     * Create class instances.
     *
     * @param int $throttleTtl
     */
    public function __construct(
        TokenRepositoryInterface $repository,
        CanResetPasswordProvider $users,
        string $key,
        callable $dispatcher = null,
        $throttleTtl = 60
    ) {
        $this->repository = $repository;
        $this->users = $users;
        $this->dispatcher = $dispatcher;
        $this->tokenFactory = new PasswordResetTokenFactory($key);
        $this->throttleTtl = $throttleTtl;
    }

    /**
     * handle create password reset link.
     *
     * @param \Closure(CanResetPasswordProvider $user, TokenInterface $token): void|null $callback
     *
     * @return void
     */
    public function handle(string $sub, \Closure $callback = null)
    {
        $user = $this->users->retrieveForPasswordReset($sub);

        if (null === $user) {
            throw new UserNotFoundException($sub);
        }

        if ((null !== ($hashedToken = $this->repository->getToken($sub))) && $this->isRecentlyCreated($hashedToken)) {
            throw new ThrottleResetException($sub);
        }

        // Create the token using the token factory instance
        $token = $this->tokenFactory->create($sub);

        // Add the token to the tokens collection
        $this->repository->addToken($token);

        $callback = $callback ?? function (CanResetPassword $u, TokenInterface $t) {
            if ($this->dispatcher) {
                \call_user_func($this->dispatcher, new PasswordResetLinkCreated($u, $t));
            }
        };

        // TODO: Publish event instance
        return \call_user_func_array($callback, [$user, $token]);
    }
}
