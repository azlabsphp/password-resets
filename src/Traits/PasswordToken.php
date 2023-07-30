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

trait PasswordToken
{
    /**
     * @var string
     */
    private $token;

    /**
     * @var string
     */
    private $sub;

    /**
     * @var \DateTimeInterface
     */
    private $createdAt;

    /**
     * @var \DateTimeInterface
     */
    private $expiresAt;

    public function getSubject()
    {
        return $this->sub;
    }

    public function getToken(): string
    {
        return (string) $this->token;
    }

    public function getCreatedAt(): \DateTimeInterface
    {
        return $this->createdAt;
    }
}
