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

class TestUrlFactory
{
    /**
     * @var string
     */
    private $name;

    /**
     * Creates class instance.
     *
     * @return void
     */
    public function __construct(string $name = 'examples')
    {
        $this->name = $name ?? 'examples';
    }

    public function __invoke(string $name, $parameters = [])
    {
        $baseUrl = 'http://localhost:8000/api/v1/'.$this->name;

        if (empty($parameters)) {
            return $baseUrl;
        }

        $baseUrl .= '?';

        foreach ($parameters as $key => $value) {
            // code...
            $baseUrl .= sprintf('%s=%s', (string) $key, (string) $value);
        }

        return $baseUrl;
    }
}
