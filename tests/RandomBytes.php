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

class RandomBytes
{
    /**
     * @var string
     */
    private $value;

    /**
     * Creates random bytes instance.
     */
    public function __construct(int $bytes = 32)
    {
        $this->value = str_replace('=', '', str_replace([\chr(92), '+', \chr(47), \chr(38)], '.', base64_encode(openssl_random_pseudo_bytes($bytes))));
    }

    public function __toString()
    {
        return (string) $this->value;
    }
}
