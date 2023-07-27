<?php

use Drewlabs\Passwords\PasswordResetToken;
use Drewlabs\Passwords\PasswordResetTokenEncoder;
use Drewlabs\Passwords\Tests\RandomBytes;
use PHPUnit\Framework\TestCase;

class PasswordResetTokenEncoderTest extends TestCase
{
    public function test_password_reset_token_encoder_decode_return_token_with_encoded_using_encode()
    {
        $encoder = new PasswordResetTokenEncoder;

        $string = $encoder->encode(new PasswordResetToken('user@example.com', $bytes = new RandomBytes, $createdAt = new DateTimeImmutable));

        $passwordToken = $encoder->decode($string);

        $this->assertEquals($createdAt->format('Y-m-d H:i:s'), $passwordToken->getCreatedAt()->format('Y-m-d H:i:s'));
        $this->assertEquals('user@example.com', $passwordToken->getSubject());
        $this->assertEquals((string)$bytes, (string)$passwordToken->getToken());
    }
}
