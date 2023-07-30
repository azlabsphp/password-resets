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

use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\CanResetPasswordProvider as AbstractCanResetPasswordProvider;
use Drewlabs\Passwords\Tests\CanResetPassword as TestsCanResetPassword;

class CanResetPasswordProvider implements AbstractCanResetPasswordProvider
{
    public function retrieveForPasswordReset(string $sub): ?CanResetPassword
    {
        if ('user@example.com' === $sub) {
            return new TestsCanResetPassword();
        }

        return null;
    }
}
