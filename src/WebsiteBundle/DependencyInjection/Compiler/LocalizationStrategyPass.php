<?php

namespace Rabble\WebsiteBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class LocalizationStrategyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $localizationStrategy = $container->getParameter('rabble_website.localization_strategy');
        $strategyIds = $container->findTaggedServiceIds('rabble_website.localization_strategy');
        foreach ($strategyIds as $id => $tags) {
            foreach ($tags as $tag) {
                if ($tag['alias'] === $localizationStrategy) {
                    $container->setAlias('rabble_website.routing.localization_strategy.default', $id);

                    return;
                }
            }
        }
    }
}
