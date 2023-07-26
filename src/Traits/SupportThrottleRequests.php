<?php

namespace Drewlabs\Passwords\Traits;

use Drewlabs\Passwords\Contracts\HashedTokenInterface;

trait SupportThrottleRequests
{
    /**
     * Number of before redefining the token
     * 
     * @var int
     */
    private $throttleTtl;

    /**
     * Check if the token is recently created
     * 
     * @param HashedTokenInterface $token 
     * @return bool 
     */
    public function isRecentlyCreated(HashedTokenInterface $token)
    {
        if (null === $token) {
            return false;
        }

        if ($this->throttleTtl <= 0) {
            return false;
        }
        return time() < \DateTimeImmutable::createFromInterface($token->getCreatedAt())->modify(sprintf("+%d seconds", $this->throttleTtl))->getTimestamp();
    }
}
