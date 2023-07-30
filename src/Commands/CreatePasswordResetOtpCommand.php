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
use Drewlabs\Passwords\Contracts\TokenRepositoryInterface;
use Drewlabs\Passwords\Events\PasswordResetOtpCreated;
use Drewlabs\Passwords\Exceptions\ThrottleResetException;
use Drewlabs\Passwords\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\Otp;
use Drewlabs\Passwords\OtpPasswordResetTokenFactory;
use Drewlabs\Passwords\Traits\SupportThrottleRequests;

class CreatePasswordResetOtpCommand
{
    use SupportThrottleRequests;

    /**
     * @var OtpPasswordResetTokenFactory
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
     * Creates class instance.
     *
     * @param int $throttleTtl
     */
    public function __construct(
        TokenRepositoryInterface $repository,
        CanResetPasswordProvider $users,
        callable $dispatcher = null,
        $throttleTtl = 60
    ) {
        $this->repository = $repository;
        $this->users = $users;
        $this->dispatcher = $dispatcher;
        $this->tokenFactory = new OtpPasswordResetTokenFactory();
        $this->throttleTtl = $throttleTtl;
    }

    /**
     * handle create password reset for otp method.
     *
     * @param \Closure(CanResetPassword $user, string $otp): void|null $callback
     *
     * @throws UserNotFoundException
     * @throws ThrottleResetException
     *
     * @return mixed
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
        $otp = (string)(new Otp);
        $token = $this->tokenFactory->create($sub, $otp);

        // Add the token to the tokens collection
        $this->repository->addToken($token);

        $callback = $callback ?? function (CanResetPassword $user, string $otp) {
            if ($this->dispatcher) {
                \call_user_func($this->dispatcher, new PasswordResetOtpCreated($user, $otp));
            }
        };

        return \call_user_func($callback, $user, $otp);
    }
}
