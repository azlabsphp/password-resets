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

use Drewlabs\Passwords\Tests\RandomBytes;
use Drewlabs\Passwords\UrlFactory;
use PHPUnit\Framework\TestCase;

class UrlFactoryTest extends TestCase
{
    public function test_url_factory_create_function()
    {
        $ranbomBytes = new RandomBytes();
        $factory = new UrlFactory(static function (string $name, $parameters = []) {
            $baseUrl = 'http://localhost:8000/api/v1/posts';

            if (empty($parameters)) {
                return $baseUrl;
            }

            $baseUrl .= '?';

            foreach ($parameters as $key => $value) {
                // code...
                $baseUrl .= sprintf('%s=%s', (string) $key, (string) $value);
            }

            return $baseUrl;
        }, false);

        $this->assertSame('http://localhost:8000/api/v1/posts?token='.(string) $ranbomBytes, $factory->create('posts', ['token' => $ranbomBytes]));
        $this->assertSame('http://localhost:8000/api/v1/posts', $factory->create('posts', []));
    }

    public function test_secure_url_factory_create()
    {
        $ranbomBytes = new RandomBytes();
        $factory = new UrlFactory(static function (string $name, $parameters = []) {
            $baseUrl = 'http://localhost:8000/api/v1/posts';

            if (empty($parameters)) {
                return $baseUrl;
            }

            $baseUrl .= '?';

            foreach ($parameters as $key => $value) {
                // code...
                $baseUrl .= sprintf('%s=%s', (string) $key, (string) $value);
            }

            return $baseUrl;
        }, true);

        $this->assertSame('https://localhost:8000/api/v1/posts?token='.(string) $ranbomBytes, $factory->create('posts', ['token' => $ranbomBytes]));
        $this->assertSame('https://localhost:8000/api/v1/posts', $factory->create('posts', []));
    }
}
