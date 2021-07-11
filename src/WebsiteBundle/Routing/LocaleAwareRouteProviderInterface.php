<?php

namespace Rabble\WebsiteBundle\Routing;

use Symfony\Cmf\Component\Routing\RouteProviderInterface;
use Symfony\Contracts\Translation\LocaleAwareInterface;

interface LocaleAwareRouteProviderInterface extends RouteProviderInterface, LocaleAwareInterface
{
}
