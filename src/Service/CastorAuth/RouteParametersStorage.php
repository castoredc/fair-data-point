<?php
declare(strict_types=1);

namespace App\Service\CastorAuth;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RouteParametersStorage
{
    const PRE_REDIRECT_ROUTE_KEY = 'SESSION_PRE_REDIRECT_ROUTE_KEY';

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * RouteParametersStorage constructor.
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }


    public function getRouteParameters(): RouteParameters
    {
        $route = json_decode((string)$this->session->get(static::PRE_REDIRECT_ROUTE_KEY), true);
        if (empty($route) || !is_array($route)) {
            return new RouteParameters('/', []);
        }

        return new RouteParameters($route['name'], $route['parameters']);

    }

    public function setRouteParameters(RouteParameters $routeParameters)
    {
        $this->session->set(self::PRE_REDIRECT_ROUTE_KEY, json_encode($routeParameters));
    }

    public function clearRouteParameters()
    {
        $this->session->set(static::PRE_REDIRECT_ROUTE_KEY, null);
    }
}
