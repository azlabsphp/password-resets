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

namespace Drewlabs\Passwords;

use Drewlabs\Passwords\Contracts\TokenInterface;
use Drewlabs\Passwords\Traits\PasswordToken as PasswordTokenMixin;

class PasswordResetToken implements TokenInterface
{
    use PasswordTokenMixin;

    /**
     * Creates password token class instance.
     *
     * @param mixed $sub
     */
    public function __construct($sub, string $token, \DateTimeInterface $createdAt)
    {
        $this->token = $token;
        $this->createdAt = $createdAt;
        $this->sub = $sub;
    }
}
