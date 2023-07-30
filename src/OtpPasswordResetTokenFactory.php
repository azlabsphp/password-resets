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

use Drewlabs\Passwords\Contracts\TokenInterface;

class OtpPasswordResetTokenFactory
{
    /**
     * Creates factory instance.
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Creates password token instance.
     *
     * @param string $sub
     */
    public function create($sub, string $otp): TokenInterface
    {
        $token = $this->base64UrlEncode(sprintf('otp_pass_token(%s, %s)', (string) $sub, (string) $otp));

        // return token static instance
        return new PasswordResetToken($sub, $token, new \DateTimeImmutable());
    }

    /**
     * Return a base64 url encoded string.
     *
     * @return string
     */
    private function base64UrlEncode(string $string)
    {
        return rtrim(strtr(base64_encode($string), '+/', '-_'), '=');
    }
}
