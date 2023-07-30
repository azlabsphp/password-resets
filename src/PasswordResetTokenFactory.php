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

class PasswordResetTokenFactory
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var string
     */
    private $algo;

    /**
     * Creates factory instance.
     *
     * @param string $algo
     *
     * @throws \InvalidArgumentException
     *
     * @return void
     */
    public function __construct(string $key, $algo = 'sha256')
    {
        if (str_starts_with($key, 'base64:')) {
            $key = base64_decode(substr($key, 7), true);
        }
        if (false === $key) {
            throw new \InvalidArgumentException(sprintf('%s is not a valid secret key string', $key));
        }
        $this->key = $key;
        $this->algo = $algo ?? 'sha256';
    }

    /**
     * Creates password token instance.
     *
     * @param string $sub
     */
    public function create($sub): TokenInterface
    {
        $token = hash_hmac($this->algo ?? 'sha256', $this->newKey(40), $this->key);
        $createdAt = \DateTimeImmutable::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s'));
        // return token static instance
        return new PasswordResetToken((string) $sub, $token, $createdAt);
    }

    /**
     * create new random base64 encoded bytes.
     *
     * @return string
     */
    private function newKey(int $bytes)
    {
        return str_replace('=', '', str_replace([\chr(92), '+', \chr(47), \chr(38)], '.', base64_encode(openssl_random_pseudo_bytes($bytes))));
    }
}
