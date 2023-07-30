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

class UrlFactory
{
    /**
     * @var bool
     */
    private $secure;

    /**
     * @var callable
     */
    private $urlFactory;

    /**
     * @param bool $secure
     *
     * @return void
     */
    public function __construct(callable $urlFactory, $secure = true)
    {
        $this->urlFactory = $urlFactory;
        $this->secure = $secure;
    }

    /**
     * Callable interface to the url `create` method.
     *
     * @param array $parameters
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function __invoke(string $name, $parameters = [], bool $absolute = true)
    {
        return $this->create($name, $parameters ?? [], $absolute);
    }

    /**
     * Wrap {@see URL} facade replacing url scheme if app url scheme is https and the generated
     * url scheme is http.
     *
     * @param array     $parameters
     * @param bool|null $absolute
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function create(string $name, $parameters = [], bool $absolute = true)
    {
        $url = \call_user_func($this->urlFactory, $name, $parameters, $absolute);

        // If the scheme is not provided, we don't continue any further as the url is not a valid url
        if (false === ($scheme = parse_url($url, \PHP_URL_SCHEME))) {
            return $url;
        }

        if ('http' !== substr($scheme, 0, 4)) {
            return $url;
        }

        // If the application url scheme is https and the generated url scheme is http
        // We replace the http:// of the generated url with https://
        if (((0 === ($pos = strpos($url, 'http://'))) || 'http' === $scheme) && $this->secure) {
            // We replace the 'http://' with 'https://' scheme
            return null !== ($subpath = mb_substr($url, $pos + mb_strlen('http://'))) ? 'https://'.$subpath : $url;
        }

        return $url;
    }
}
