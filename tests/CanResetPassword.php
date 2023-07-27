<?php

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
