<?php

namespace Rabble\WebsiteBundle\Routing;

use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\Matcher\RequestMatcherInterface;

class RequestMatcher implements RequestMatcherInterface
{
    private RouteProviderInterface $routeProvider;

    public function __construct(RouteProviderInterface $provider)
    {
        $this->routeProvider = $provider;
    }

    public function matchRequest(Request $request)
    {
        $collection = $this->routeProvider->getRouteCollectionForRequest($request);
        if (0 === count($collection)) {
            throw new ResourceNotFoundException();
        }
        $route = current($collection->all());

        return $route->getDefaults();
    }

    public function setRouteProvider(RouteProviderInterface $provider): void
    {
        $this->routeProvider = $provider;
    }
}
