<?php

namespace App\Support;

use InvalidArgumentException;

class UrlFactory
{
    /**
     * 
     * @var bool
     */
    private $secure;

    /**
     * @var callable
     */
    private $urlFactory;

    /**
     * @param callable $urlFactory
     * @param bool $secure 
     * @return void 
     */
    public function __construct(callable $urlFactory, $secure = true)
    {
        $this->urlFactory  = $urlFactory;
        $this->secure = $secure;
    }

    /**
     * Callable interface to the url `create` method
     * 
     * @param string $name 
     * @param array $parameters 
     * @param bool $absolute 
     * @return string 
     * @throws InvalidArgumentException 
     */
    public function __invoke(string $name, $parameters = [], bool $absolute = true)
    {
        return $this->create($name, $parameters ?? [], $absolute);
    }

    /**
     * Wrap {@see URL} facade replacing url scheme if app url scheme is https and the generated
     * url scheme is http
     *
     * @param string $name
     * @param array $parameters
     * @param bool|null $absolute
     * @return string
     * @throws InvalidArgumentException
     */
    public function create(string $name, $parameters = [], bool $absolute = true)
    {
        $url = call_user_func($this->urlFactory, $name, $parameters, $absolute);

        // If the scheme is not provided, we don't continue any further as the url is not a valid url
        if (false === ($scheme = parse_url($url, PHP_URL_SCHEME))) {
            return $url;
        }

        if (substr($scheme, 0, 4) !== 'http') {
            return $url;
        }

        // If the application url scheme is https and the generated url scheme is http
        // We replace the http:// of the generated url with https://
        if (((0 === ($pos = strpos($url, 'http://'))) || $scheme === 'http') && $this->secure) {
            // We replace the 'http://' with 'https://' scheme
            return null !== ($subpath = mb_substr($url, $pos + mb_strlen('http://'))) ? 'https://' . $subpath : $url;
        }
        return $url;
    }
}
