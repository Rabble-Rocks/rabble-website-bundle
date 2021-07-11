<?php

namespace Rabble\WebsiteBundle\Routing\Localization;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class PathLocalizationStrategy implements LocalizationStrategyInterface
{
    private array $enabledLocales;

    public function __construct(array $enabledLocales)
    {
        $this->enabledLocales = $enabledLocales;
    }

    public function fromRequest(Request $request): Localization
    {
        $pathInfo = $request->getPathInfo();
        preg_match('/^\/([^\/]+)/', $pathInfo, $matches);
        if (!isset($matches[1]) || !in_array($matches[1], $this->enabledLocales)) {
            return new Localization($request->getLocale(), $pathInfo);
        }

        return new Localization($matches[1], substr($pathInfo, strlen($matches[1]) + 1));
    }

    public function enhanceRoute(Route $route): void
    {
        $route->setPath('/{_locale}'.$route->getPath());
    }
}
