<?php

namespace Rabble\WebsiteBundle\Routing;

use Psr\Log\LoggerInterface;
    use Symfony\Component\Routing\Generator\UrlGenerator as BaseUrlGenerator;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class UrlGenerator extends BaseUrlGenerator
{
    private LocaleAwareRouteProviderInterface $routeProvider;
    private string $defaultLocale;

    public function __construct(
        LocaleAwareRouteProviderInterface $provider,
        RequestContext $context,
        LoggerInterface $logger,
        string $defaultLocale
    ) {
        $this->routeProvider = $provider;
        parent::__construct(new RouteCollection(), $context, $logger, $defaultLocale);
        $this->defaultLocale = $defaultLocale;
    }

    public function generate(string $name, array $parameters = [], int $referenceType = self::ABSOLUTE_PATH): string
    {
        $locale = $parameters['_locale']
            ?? $this->context->getParameter('_locale')
                ?: $this->defaultLocale;
        $this->routeProvider->setLocale($locale);

        $route = $this->routeProvider->getRouteByName($name);

        $compiledRoute = $route->compile();

        $defaults = $route->getDefaults();
        $variables = $compiledRoute->getVariables();

        if (isset($defaults['_canonical_route'], $defaults['_locale'])) {
            if (!\in_array('_locale', $variables, true)) {
                unset($parameters['_locale']);
            } elseif (!isset($parameters['_locale'])) {
                $parameters['_locale'] = $defaults['_locale'];
            }
        }

        return $this->doGenerate($variables, $defaults, $route->getRequirements(), $compiledRoute->getTokens(), $parameters, $name, $referenceType, $compiledRoute->getHostTokens(), $route->getSchemes());
    }
}
