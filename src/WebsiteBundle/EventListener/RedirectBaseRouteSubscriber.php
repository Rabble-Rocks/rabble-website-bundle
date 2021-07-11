<?php

namespace Rabble\WebsiteBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\RouterInterface;

/**
 * Redirects the user if the entry URI does not match the current route path.
 */
class RedirectBaseRouteSubscriber implements EventSubscriberInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'onKernelRequest',
        ];
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$request->attributes->has('contentDocument')) {
            return;
        }
        $uri = $this->router->generate($request->attributes->get('_route'));
        if ($request->getPathInfo() !== $uri) {
            $event->setResponse(new RedirectResponse($uri, 301));
        }
    }
}
