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

class ResetPassword
{
    /**
     * @var string
     */
    private $password;

    /**
     * @var CanResetPassword
     */
    private $user;

    /**
     * Create event class instance.
     */
    public function __construct(CanResetPassword $user, string $password)
    {
        $this->user = $user;
        $this->password = $password;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getPassword()
    {
        return $this->password;
    }
}
