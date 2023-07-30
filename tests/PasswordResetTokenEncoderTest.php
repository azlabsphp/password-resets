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

use Drewlabs\Passwords\PasswordResetToken;
use Drewlabs\Passwords\PasswordResetTokenEncoder;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenEncoderTest extends TestCase
{
    public function test_password_reset_token_encoder_decode_return_token_with_encoded_using_encode()
    {
        $encoder = new PasswordResetTokenEncoder();
        $bytes = new RandomBytes();
        $string = $encoder->encode(new PasswordResetToken('user@example.com', (string)$bytes, $createdAt = new DateTimeImmutable()));

        $passwordToken = $encoder->decode($string);

        $this->assertSame($createdAt->format('Y-m-d H:i:s'), $passwordToken->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertSame('user@example.com', $passwordToken->getSubject());
        $this->assertSame((string) $bytes, (string) $passwordToken->getToken());
    }
}
