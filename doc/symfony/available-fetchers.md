# Available fetchers

| type  | Description | option |
|---|---|---|
| doctrine | Retrieve overrides from a SeoOverride entity | None |
| in_memory | Retrieve overrides from an array stored in memory | - "overrides": mandatory, the array containing the overrides  |
| php | Retrieve overrides from a php file | - "include_path": mandatory, path of the file to include  |

## doctrine

The `doctrine` fetcher does not have option. You only need to add our entity in
Doctrine ORM's mappings:

```yaml
doctrine:
    # ...
    orm:
        # ...
        mappings:
            # ...
            SeoOverride:
                type: yml
                is_bundle: false
                dir: '%kernel.root_dir%/../vendor/jolicode/seo-override/src/Bridge/Doctrine/Resources/config/doctrine'
                prefix: Joli\SeoOverride\Bridge\Doctrine\Entity
                alias: SeoOverride
```

Then, add the fetcher:

```yaml
seo_override:
    fetchers:
        - { type: doctrine }
```

Our bundle also supports a short syntax for fetcher with no option needed:

```yaml
seo_override:
    fetchers:
        - doctrine
```

## in_memory

The `in_memory` fetcher is configured through the `overrides` option. This
option should be formatted this way:

```yaml
seo_override:
    fetchers:
        -
            type: in_memory
            overrides:
                domain_alias_1:
                    '/url1':
                        # All properties of Seo can optionally be defined (title, description, keywords, robots, etc)
                        title: 'Title url 1'
                        description: 'Description url 1'
                    '/url2':
                        title: 'Title url 2'
                '': # Empty alias means that every domain will match the following overrides
                    '/url3':
                        title: 'Title url 3'
```

## php

To use the `php` fetcher with a file to include - located for example at
`%kernel.root_dir%/config/overrides.php` - you should configure it like that:

```yaml
seo_override:
    fetchers:
        - { type: php, include_path: '%kernel.root_dir%/config/overrides.php' }
```

The file included should return a PHP array formatted like this:

```php
<?php

return [
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
```
