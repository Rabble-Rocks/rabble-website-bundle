<?php

namespace Rabble\WebsiteBundle;

use Rabble\WebsiteBundle\DependencyInjection\Compiler\LocalizationStrategyPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class RabbleWebsiteBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new LocalizationStrategyPass());
    }
}
