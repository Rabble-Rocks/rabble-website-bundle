<?php

namespace Rabble\WebsiteBundle\Routing;

use Symfony\Cmf\Bundle\RoutingBundle\Routing\DynamicRouter;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class WebsiteRouter extends DynamicRouter
{
    public const CONTENT_KEY = 'contentDocument';

    public const CONTENT_TEMPLATE = 'template';

    private RequestStack $requestStack;

    public function match($pathinfo)
    {
        throw new \LogicException('This function is deprecated.');
    }

    public function matchRequest(Request $request): array
    {
        $defaults = parent::matchRequest($request);

        return $this->cleanDefaults($defaults, $request);
    }

    /**
     * Set the request stack so that we can find the current request.
     */
    public function setRequestStack(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getRequest(): Request
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        if (!$currentRequest) {
            throw new ResourceNotFoundException('There is no request in the request stack');
        }

        return $currentRequest;
    }
}
