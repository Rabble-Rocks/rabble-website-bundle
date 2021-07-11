# Rabble Website Bundle
The website bundle is responsible for routing and linking content from the content bundle to the website.

# Installation
Install the bundle by running
```sh
composer require rabble/website-bundle
```

Add the following class to your `config/bundles.php` file:
```php
return [
    ...
    Rabble\WebsiteBundle\RabbleWebsiteBundle::class => ['all' => true],
]
```
