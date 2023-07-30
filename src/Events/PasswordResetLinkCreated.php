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

namespace Drewlabs\Passwords\Events;

use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\TokenInterface;

class PasswordResetLinkCreated
{
    /**
     * @var TokenInterface
     */
    private $token;

    /**
     * @var CanResetPassword
     */
    private $user;

    /**
     * Creates password reset link created event instance.
     */
    public function __construct(CanResetPassword $user, TokenInterface $token)
    {
        $this->user = $user;
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getUser()
    {
        return $this->user;
    }
}
