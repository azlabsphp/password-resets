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

class PasswordResetOtpCreated
{
    /**
     * @var string
     */
    private $otp;

    /**
     * @var CanResetPassword
     */
    private $user;

    /**
     * Creates password reset otp created event instance.
     *
     * @return void
     */
    public function __construct(CanResetPassword $user, string $otp)
    {
        $this->user = $user;
        $this->otp = $otp;
    }

    public function getOTP()
    {
        return $this->otp;
    }

    public function getUser()
    {
        return $this->user;
    }
}
