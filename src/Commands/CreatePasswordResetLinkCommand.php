<?php

namespace App\Support\Commands;

use App\Contracts\CanResetPassword;
use App\Contracts\CanResetPasswordProvider;
use App\Contracts\TokenInterface;
use App\Contracts\TokenRepositoryInterface;
use App\Exceptions\ThrottleResetException;
use App\Exceptions\UserNotFoundException;
use App\Support\Events\PasswordResetLinkCreated;
use App\Support\PasswordTokenFactory;
use App\Support\Traits\SupportThrottleRequests;
use Closure;

class CreatePasswordResetLinkCommand
{
    use SupportThrottleRequests;

    /**
     * @var callable
     */
    private $url;

    /**
     * @var PasswordTokenFactory
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
     * Create class instances
     * 
     * @param TokenRepositoryInterface $repository 
     * @param CanResetPasswordProvider $users 
     * @param PasswordTokenFactory $tokenFactory 
     * @param callable $dispatcher 
     * @param callable $url 
     * @param int $throttleTtl 
     * @return void 
     */
    public function __construct(
        TokenRepositoryInterface $repository,
        CanResetPasswordProvider $users,
        PasswordTokenFactory $tokenFactory,
        callable $dispatcher,
        callable $url,
        $throttleTtl = 60
    ) {
        $this->repository = $repository;
        $this->users = $users;
        $this->dispatcher = $dispatcher;
        $this->tokenFactory = $tokenFactory;
        $this->url = $url;
        $this->throttleTtl = $throttleTtl;
    }

    /**
     * 
     * @param string $sub 
     * @param string $route 
     * @param null|Closure(CanResetPasswordProvider $user, TokenInterface $token): void $callback 
     * @return void 
     */
    public function handle(string $sub, string $route, \Closure $callback = null)
    {
        $user = $this->users->retrieveForPasswordReset($sub);

        if (null === $user) {
            throw new UserNotFoundException($sub);
        }

        if ($this->isRecentlyCreated($this->repository->getToken($sub))) {
            throw new ThrottleResetException($sub);
        }

        // Create the token using the token factory instance
        $token = $this->tokenFactory->create($sub);

        // Add the token to the tokens collection
        $this->repository->addToken($token);

        $callback = $callback ?? function (CanResetPassword $user, TokenInterface $token) use ($route) {
            call_user_func($this->dispatcher, new PasswordResetLinkCreated($user, call_user_func($this->url, $route, ['token' => $token])));
        };

        // TODO: Publish event instance
        return call_user_func($callback, $user, $token);
    }
}
