<?php

namespace Rabble\WebsiteBundle\Routing\Localization;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Route;

interface LocalizationStrategyInterface
{
    public function fromRequest(Request $request): Localization;

    public function enhanceRoute(Route $route): void;
}
