<?php

namespace Drewlabs\Passwords\Commands;

use Drewlabs\Passwords\Exceptions\ThrottleResetException;
use Drewlabs\Passwords\Exceptions\UserNotFoundException;
use Drewlabs\Passwords\Events\PasswordResetLinkCreated;
use Drewlabs\Passwords\PasswordResetTokenFactory;
use Drewlabs\Passwords\Traits\SupportThrottleRequests;
use Closure;
use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\CanResetPasswordProvider;
use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Contracts\TokenRepositoryInterface;

class CreatePasswordResetCommand
{
    use SupportThrottleRequests;

    /**
     * @var callable
     */
    private $url;

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
     * Create class instances
     * 
     * @param TokenRepositoryInterface $repository 
     * @param CanResetPasswordProvider $users 
     * @param PasswordResetTokenFactory $tokenFactory 
     * @param callable $url 
     * @param callable|null $dispatcher 
     * @param int $throttleTtl 
     * @return void 
     */
    public function __construct(
        TokenRepositoryInterface $repository,
        CanResetPasswordProvider $users,
        string $key,
        callable $url = null,
        callable $dispatcher = null,
        $throttleTtl = 60
    ) {
        $this->repository = $repository;
        $this->users = $users;
        $this->dispatcher = $dispatcher;
        $this->tokenFactory = new PasswordResetTokenFactory($key);
        $this->url = $url;
        $this->throttleTtl = $throttleTtl;
    }

    /**
     * 
     * @param string $sub 
     * @param string|null|Closure(CanResetPasswordProvider $user, TokenInterface $token): void $routeOrCallable 
     * @param null|Closure(CanResetPasswordProvider $user, TokenInterface $token): void $callback 
     * @return void 
     */
    public function handle(string $sub, $routeOrCallable = null, \Closure $callback = null)
    {
        $user = $this->users->retrieveForPasswordReset($sub);

        // Case two parameters are passed as argument and the second argument is a callable instance
        // we set the callback to be equals to the second argument and the route to be null
        if (null === $callback && !is_string($routeOrCallable) && is_callable($routeOrCallable)) {
            $callback = $routeOrCallable;
            $routeOrCallable = null;
        }

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

        $callback = $callback ?? function (CanResetPassword $user, string $url) {
            if ($this->dispatcher) {
                call_user_func($this->dispatcher, new PasswordResetLinkCreated($user, $url));
            }
        };

        // construc the callback arguments
        $args = null !== $routeOrCallable && null !== $this->url ? [$user, call_user_func($this->url, $routeOrCallable, ['token' => $token->getToken()]), $token] : [$user, $token];

        // TODO: Publish event instance
        return call_user_func_array($callback, $args);
    }
}
