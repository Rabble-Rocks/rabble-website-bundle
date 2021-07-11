<?php

namespace Rabble\WebsiteBundle\Routing\Localization;

class Localization
{
    private string $locale;
    private string $slug;

    public function __construct(string $locale, string $slug)
    {
        $this->locale = $locale;
        $this->slug = $slug;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }
}
