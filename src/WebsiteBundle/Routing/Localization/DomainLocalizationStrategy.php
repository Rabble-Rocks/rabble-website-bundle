<?php

namespace Rabble\WebsiteBundle\Routing\Localization;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

class DomainLocalizationStrategy implements LocalizationStrategyInterface
{
    private array $enabledLocales;
    private array $domainMapping;

    public function __construct(array $enabledLocales, array $domainMapping)
    {
        $this->enabledLocales = $enabledLocales;
        $this->domainMapping = $domainMapping;
    }

    public function fromRequest(Request $request): Localization
    {
        $host = $request->getHost();
        if (isset($this->domainMapping[$host]) && in_array($this->domainMapping[$host], $this->enabledLocales)) {
            return new Localization($this->domainMapping[$host], $request->getPathInfo());
        }

        return new Localization($request->getLocale(), $request->getPathInfo());
    }

    public function enhanceRoute(Route $route): void
    {
        $localeMapping = array_flip($this->domainMapping);
        if (!$route->hasDefault('_locale') || !isset($localeMapping[$route->getDefault('_locale')])) {
            return;
        }
        $host = $localeMapping[$route->getDefault('_locale')];
        $route->setHost($host);
    }
}
