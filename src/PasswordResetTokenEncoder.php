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
use Tuupola\Base62Proxy;

class PasswordResetTokenEncoder
{
    public function encode(TokenInterface $token)
    {
        return Base62Proxy::encode(sprintf('%s.%s.%s', $this->base64UriEncode($token->getSubject()), $this->base64UriEncode($token->getCreatedAt()->format(\DateTimeImmutable::ATOM)), $this->base64UriEncode($token->getToken())));
    }

    public function decode(string $token)
    {
        if (\is_string($string = Base62Proxy::decode($token))) {
            [$sub, $at, $value] = explode('.', $string);

            return new PasswordResetToken($this->base64UriDecode($sub), $this->base64UriDecode($value), \DateTimeImmutable::createFromFormat(\DateTimeImmutable::ATOM, $this->base64UriDecode($at)));
        }

        return null;
    }

    /**
     * base64 uri encoded string.
     *
     * @return string
     */
    private function base64UriEncode(string $value)
    {
        $base64Url = strtr(base64_encode($value), '+/', '-_');

        return rtrim($base64Url, '=');
    }

    /**
     * base64 uri decode string.
     *
     * @return string
     */
    private function base64UriDecode(string $value)
    {
        return base64_decode(strtr($value, '-_', '+/'), true);
    }
}
