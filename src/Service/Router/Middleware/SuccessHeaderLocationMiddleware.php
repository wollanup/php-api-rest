<?php
/**
 * Created by PhpStorm.
 * User: steve
 * Date: 11/10/17
 * Time: 16:24
 */

namespace Eukles\Service\Router\Middleware;

use Eukles\Entity\EntityFactoryConfig;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Http\Response;
use Slim\Http\Uri;

/**
 * Class EntityFetch
 *
 * @package Eukles\Entity\Middleware
 */
class SuccessHeaderLocationMiddleware
{

    const LOCATION_PATTERN = '#\{(\w+)\}#';
    /**
     * @var EntityFactoryConfig
     */
    protected $config;
    /**
     * @var string
     */
    protected $location;

    public function __construct(string $location, EntityFactoryConfig $config = null)
    {
        $this->location = $location;
        $this->config   = $config;
    }

    /**
     * @param $request
     * @param $response
     * @param $next
     *
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, $next): ResponseInterface
    {
        /** @var Response $response */
        $response = $next($request, $response);

        if (!$response->isSuccessful()) {
            return $response;
        }

        /** @var Uri $uri */
        $uri  = $request->getUri();
        $base = $uri->getScheme() . '://';
        $base .= $uri->getHost();
        $base .= $uri->getPort() ? ':' . $uri->getPort() : "";
        $base .= $uri->getBasePath();

        $this->location = $base . $this->location;

        if (null === $this->config) {
            return $response->withHeader('Location', $this->location);
        }

        $obj = $request->getAttribute($this->config->getParameterToInjectInto());
        if ($obj === null) {
            throw new \RuntimeException("Parameter given in config object not found in request attributes");
        }

        preg_match(self::LOCATION_PATTERN, $this->location, $matches);

        if (!isset($matches[1])) {
            # Should not happen, why pass a config object without pattern replacement ? But in case, just allow that.
            return $response->withHeader('Location', $this->location);
        }

        $getter = 'get' . ucfirst($matches[1]);
        if (method_exists($obj, $getter)) {
            $value = call_user_func([$obj, $getter]);
        } else {
            throw new \RuntimeException('Getter method not found in object');
        }

        return $response->withHeader('Location', preg_replace(self::LOCATION_PATTERN, $value, $this->location));
    }
}
