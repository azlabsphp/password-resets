<?php

namespace App\Support\Traits;

use App\Contracts\HashedTokenInterface;

trait SupportThrottleRequests
{
    /**
     * Number of before redefining the token
     * 
     * @var int
     */
    private $throttleTtl;

    public function isRecentlyCreated(HashedTokenInterface $token)
    {
        if (null === $token) {
            return false;
        }

        if ($this->throttleTtl <= 0) {
            return false;
        }
        return time() < \DateTimeImmutable::createFromInterface($token->getCreatedAt())->modify(sprintf("+%d seconds", $this->throttleTtl));
    }
}
