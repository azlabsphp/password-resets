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

namespace Drewlabs\Passwords\Traits;

use Drewlabs\Passwords\Contracts\HashedTokenInterface;

trait SupportThrottleRequests
{
    /**
     * Number of before redefining the token.
     *
     * @var int
     */
    private $throttleTtl;

    /**
     * Check if the token is recently created.
     *
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

        return time() < \DateTimeImmutable::createFromInterface($token->getCreatedAt())->modify(sprintf('+%d seconds', $this->throttleTtl))->getTimestamp();
    }
}
