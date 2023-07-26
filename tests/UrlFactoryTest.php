<?php

use Drewlabs\Passwords\Tests\RandomBytes;
use Drewlabs\Passwords\UrlFactory;
use PHPUnit\Framework\TestCase;

class UrlFactoryTest extends TestCase
{

    public function test_url_factory_create_function()
    {
        $ranbomBytes = new RandomBytes;
        $factory = new UrlFactory(function(string $name, $parameters = []) {
            $baseUrl = 'http://localhost:8000/api/v1/posts';

            if (empty($parameters)) {
                return $baseUrl;
            }

            $baseUrl .= '?';

            foreach ($parameters as $key => $value) {
                # code...
                $baseUrl .= sprintf("%s=%s", (string)$key, (string)$value);
            }

            return $baseUrl;
        }, false);

        $this->assertEquals('http://localhost:8000/api/v1/posts?token=' . (string)$ranbomBytes, $factory->create('posts', ['token' => $ranbomBytes]));
        $this->assertEquals('http://localhost:8000/api/v1/posts' , $factory->create('posts', []));
    }

    public function test_secure_url_factory_create()
    {
        $ranbomBytes = new RandomBytes;
        $factory = new UrlFactory(function(string $name, $parameters = []) {
            $baseUrl = 'http://localhost:8000/api/v1/posts';

            if (empty($parameters)) {
                return $baseUrl;
            }

            $baseUrl .= '?';

            foreach ($parameters as $key => $value) {
                # code...
                $baseUrl .= sprintf("%s=%s", (string)$key, (string)$value);
            }

            return $baseUrl;
        }, true);

        $this->assertEquals('https://localhost:8000/api/v1/posts?token=' . (string)$ranbomBytes, $factory->create('posts', ['token' => $ranbomBytes]));
        $this->assertEquals('https://localhost:8000/api/v1/posts' , $factory->create('posts', []));
    }
}