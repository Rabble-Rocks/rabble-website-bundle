<?php

namespace Rabble\WebsiteBundle\Routing;

use Doctrine\Common\Collections\ArrayCollection;
use ONGR\ElasticsearchBundle\Service\IndexService;
use ONGR\ElasticsearchDSL\Query\Compound\BoolQuery;
use ONGR\ElasticsearchDSL\Query\TermLevel\TermQuery;
use Rabble\ContentBundle\Content\Structure\StructureBuilder;
use Rabble\ContentBundle\ContentType\ContentTypeManagerInterface;
use Rabble\ContentBundle\Persistence\Manager\ContentManager;
use Rabble\WebsiteBundle\Controller\DefaultController;
use Rabble\WebsiteBundle\Routing\Localization\LocalizationStrategyInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\RouteNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class RouteProvider implements LocaleAwareRouteProviderInterface
{
    /** @var ArrayCollection<IndexService> */
    private ArrayCollection $indexes;
    private LocalizationStrategyInterface $localizationStrategy;
    private ContentTypeManagerInterface $contentTypeManager;
    private ContentManager $contentManager;
    private StructureBuilder $structureBuilder;
    private string $defaultLocale;

    public function __construct(
        ArrayCollection $indexes,
        LocalizationStrategyInterface $localizationStrategy,
        ContentTypeManagerInterface $contentTypeManager,
        ContentManager $contentManager,
        StructureBuilder $structureBuilder,
        string $defaultLocale
    ) {
        $this->indexes = $indexes;
        $this->localizationStrategy = $localizationStrategy;
        $this->contentTypeManager = $contentTypeManager;
        $this->contentManager = $contentManager;
        $this->structureBuilder = $structureBuilder;
        $this->defaultLocale = $defaultLocale;
    }

    public function getRouteCollectionForRequest(Request $request): RouteCollection
    {
        $localization = $this->localizationStrategy->fromRequest($request);
        $routeCollection = new RouteCollection();
        if (!isset($this->indexes['content-'.$localization->getLocale()])) {
            return $routeCollection;
        }
        $this->contentManager->setLocale($localization->getLocale());
        /** @var IndexService $index */
        $index = $this->indexes['content-'.$localization->getLocale()];
        $search = $index->createSearch();
        $search->addQuery($bool = new BoolQuery());
        $bool->add(new TermQuery('properties.slug.keyword', $localization->getSlug(), ['case_insensitive' => true]));

        $results = $index->search($search->toArray());
        foreach ($results['hits']['hits'] as $hit) {
            $source = $hit['_source'];
            $route = new Route($source['properties']['slug'] ?? $localization->getSlug());
            $route->setDefault('_locale', $localization->getLocale());
            $this->enhanceRoute($route, $source, $hit['_id']);
            $routeCollection->add($hit['_id'], $route);
        }

        return $routeCollection;
    }

    /**
     * @param string $name
     */
    public function getRouteByName($name): Route
    {
        $content = $this->contentManager->find($name);
        if (null === $content) {
            throw new RouteNotFoundException();
        }
        $route = new Route($content->getProperties()['slug'] ?? '/');
        $route->setDefault('_locale', $this->contentManager->getLocale());
        $this->enhanceRoute($route, $this->structureBuilder->build($content), $content->getUuid());

        return $route;
    }

    /**
     * @param null|array $names
     *
     * @return Route[]
     */
    public function getRoutesByNames($names): array
    {
        $routes = [];
        if (null === $names) {
            return [];
        }
        foreach ($names as $name) {
            try {
                $routes[$name] = $this->getRouteByName($name);
            } catch (RouteNotFoundException $exception) {
            }
        }

        return $routes;
    }

    public function setLocale(string $locale)
    {
        $this->contentManager->setLocale($locale);
    }

    public function getLocale(): string
    {
        return $this->contentManager->getLocale() ?? $this->defaultLocale;
    }

    private function enhanceRoute(Route $route, array $source, string $id): void
    {
        $this->localizationStrategy->enhanceRoute($route);
        $defaults = [
            '_controller' => sprintf('%s::indexAction', DefaultController::class),
            '_template' => '@RabbleWebsite/Default/index.html.twig',
            '_route' => $id,
        ];
        if (isset($source['contentType']) && $this->contentTypeManager->has($source['contentType'])) {
            $contentType = $this->contentTypeManager->get($source['contentType']);
            if ($contentType->hasAttribute('route_defaults')) {
                $defaults = array_merge($defaults, $contentType->getAttribute('route_defaults'));
            }
        }
        $route->addDefaults($defaults);
        $content = $this->contentManager->find($id);
        if (null === $content) {
            return;
        }
        $structure = $this->structureBuilder->build($content);
        $content = array_merge([
            'id' => $id,
            'contentType' => $source['contentType'] ?? null,
            'title' => $source['title'] ?? null,
        ], $structure);
        $route->setDefault(WebsiteRouter::CONTENT_KEY, $content);
    }
}
