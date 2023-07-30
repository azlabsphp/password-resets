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

class Otp
{
    /**
     * @var int
     */
    private $value;

    /**
     * Creates otp instance.
     *
     * @throws \TypeError
     * @throws \Error
     * @throws \Exception
     */
    public function __construct()
    {
        $this->value = random_int(100000, 999999);
    }

    /**
     * Returns the otp instance as string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}
