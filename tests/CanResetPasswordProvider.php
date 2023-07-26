<?php

namespace Drewlabs\Passwords\Tests;

use Drewlabs\Passwords\Contracts\CanResetPassword;
use Drewlabs\Passwords\Contracts\CanResetPasswordProvider as AbstractCanResetPasswordProvider;
use Drewlabs\Passwords\Tests\CanResetPassword as TestsCanResetPassword;

class CanResetPasswordProvider implements AbstractCanResetPasswordProvider
{

    public function retrieveForPasswordReset(string $sub): ?CanResetPassword
    {
        if ($sub === 'user@example.com') {
            return new TestsCanResetPassword;
        }
        return null;
    }
}
