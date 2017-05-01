# Available fetchers

## DoctrineFetcher

This fetcher is provided by a Doctrine bridge. It looks for `Seo` through a
Doctrine Entity.

```php
    new Joli\SeoOverride\Bridge\Doctrine\DoctrineFetcher($doctrineRegistry);
```

We provide some Doctrine mappings for our entity and Value Object. Those
mappings are written in yaml and are located inside `vendor/jolicode/seo-override/src/Bridge/Doctrine/Resources/config/doctrine`.
You will need to tell Doctrine ORM to load them for handling `Joli\SeoOverride`
namespace.

## InMemoryFetcher

This fetcher is directly configured with all the overrides.

```php
    $seoOverrides = [
        'domain_alias_1' => [
            '/url1' => [
                 // All properties of Seo can optionally be defined (title, description, keywords, robots, etc)
                'title' => 'Title url 1',
                'description' => 'Description url 1' ,
            ],
            '/url2' => [
                'title' => 'Title url 2',
            ],
        ],
        // Empty alias means that every domain will match the following overrides
        '' => [
            '/url3' => [
                'title' => 'Title url 3',
            ],
        ],
    ];
    new Joli\SeoOverride\Fetcher\InMemoryFetcher($seoOverrides);
```

## PhpFetcher

This fetcher loads the overrides from an included file.

```php
    $includePath = __DIR__ . '/config/seo_overrides.php';
    new Joli\SeoOverride\Fetcher\PhpFetcher($includePath):
```

The included file should return a PHP array with the same structure than the
InMemoryFetcher - see above.
