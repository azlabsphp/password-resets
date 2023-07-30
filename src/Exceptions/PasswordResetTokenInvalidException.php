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

namespace Drewlabs\Passwords\Exceptions;

use Exception;

class PasswordResetTokenInvalidException extends \Exception
{
    /**
     * Create exception class instance.
     */
    public function __construct(string $token)
    {
        $message = sprintf('Password reset token %s, is either invalid or expire', $token);

        parent::__construct($message);
    }
}
