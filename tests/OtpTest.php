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

use Drewlabs\Passwords\Otp;
use PHPUnit\Framework\TestCase;

class OtpTest extends TestCase
{
    public function test_otp_to_string_return_numeric()
    {
        $otp = new Otp();
        $is_numeric = is_numeric((string)$otp);
        $this->assertTrue($is_numeric);
    }

    public function test_otp_to_string()
    {
        $otp = new Otp();
        $is_string = is_string((string)$otp);
        $this->assertTrue($is_string);
    }
}
