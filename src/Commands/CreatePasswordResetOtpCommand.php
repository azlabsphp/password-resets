<?php

namespace Drewlabs\Passwords\Commands;

use App\Exceptions\ThrottleResetException;
use App\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\Events\PasswordResetOtpCreated;
use Drewlabs\Passwords\Otp;
use Drewlabs\Passwords\OtpPasswordResetTokenFactory;
use Drewlabs\Passwords\Traits\SupportThrottleRequests;
use Closure;
use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\CanResetPasswordProvider;
use Drewlabs\Passwords\Contracts\TokenRepositoryInterface;

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
     * Creates class instance
     * 
     * @param TokenRepositoryInterface $repository 
     * @param CanResetPasswordProvider $users 
     * @param OtpPasswordResetTokenFactory $tokenFactory 
     * @param callable $dispatcher 
     * @param int $throttleTtl 
     * @return void 
     */
    public function __construct(
        TokenRepositoryInterface $repository,
        CanResetPasswordProvider $users,
        OtpPasswordResetTokenFactory $tokenFactory,
        callable $dispatcher,
        $throttleTtl = 60
    ) {
        $this->repository = $repository;
        $this->users = $users;
        $this->dispatcher = $dispatcher;
        $this->tokenFactory = $tokenFactory;
        $this->throttleTtl = $throttleTtl;
    }


    /**
     * handle create password reset for otp method
     * 
     * @param string $sub 
     * @param Closure(CanResetPassword $user, string $otp): void|null $callback 
     * @return mixed 
     * @throws UserNotFoundException 
     * @throws ThrottleResetException 
     */
    public function handle(string $sub, \Closure $callback = null)
    {
        $user = $this->users->retrieveForPasswordReset($sub);

        if (null === $user) {
            throw new UserNotFoundException($sub);
        }

        if ($this->isRecentlyCreated($this->repository->getToken($sub))) {
            throw new ThrottleResetException($sub);
        }

        // Create the token using the token factory instance
        $token = $this->tokenFactory->create($sub, $otp = new Otp);

        // Add the token to the tokens collection
        $this->repository->addToken($token);

        $callback = $callback ?? function (CanResetPassword $user, string $otp) {
            call_user_func($this->dispatcher, new PasswordResetOtpCreated($user, $otp));
        };

        return call_user_func($callback, $user, $otp);
    }
}
