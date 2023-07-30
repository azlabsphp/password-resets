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

namespace Drewlabs\Passwords\Tests;

use Drewlabs\Passwords\Contracts\CanResetPassword as AbstractCanResetPassword;

class CanResetPassword implements AbstractCanResetPassword
{
    public function getSubForPasswordResetLink(): ?string
    {
        return 'user@example.com';
    }

    public function getSubForPasswordResetOtp(): ?string
    {
        return 'user@example.com';
    }

    public function resetPassword(string $password)
    {
        return sprintf('resetting password %s\n', $password);
    }
}
