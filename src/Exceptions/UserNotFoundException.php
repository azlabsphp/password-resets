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

class UserNotFoundException extends \Exception
{
    /**
     * Creates exception class instance.
     *
     * @return void
     */
    public function __construct(string $sub)
    {
        $message = sprintf('Cannot find user %s', $sub);
        parent::__construct($message, 500);
    }
}
