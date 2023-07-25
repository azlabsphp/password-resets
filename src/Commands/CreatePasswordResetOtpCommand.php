<?php

namespace App\Support\Commands;

use App\Contracts\CanResetPassword;
use App\Contracts\CanResetPasswordProvider;
use App\Contracts\TokenRepositoryInterface;
use App\Exceptions\ThrottleResetException;
use App\Exceptions\UserNotFoundException;
use App\Support\Events\PasswordResetOtpCreated;
use App\Support\Otp;
use App\Support\OtpPasswordTokenFactory;
use App\Support\Traits\SupportThrottleRequests;
use Closure;

class CreatePasswordResetOtpCommand
{
    use SupportThrottleRequests;

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
     * Creates class instance
     * 
     * @param TokenRepositoryInterface $repository 
     * @param CanResetPasswordProvider $users 
     * @param OtpPasswordTokenFactory $tokenFactory 
     * @param callable $dispatcher 
     * @param int $throttleTtl 
     * @return void 
     */
    public function __construct(
        TokenRepositoryInterface $repository,
        CanResetPasswordProvider $users,
        OtpPasswordTokenFactory $tokenFactory,
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
