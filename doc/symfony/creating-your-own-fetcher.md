# Creating your own fetcher

Creating your own fetcher is really easy.

## The fetcher class

First, you need to **write a class** implementing the `Joli\SeoOverride\Fetcher`
interface. This interface defines only one method:

```php
interface Fetcher
{
    /**
     * @return Seo|null
     */
    public function fetch(string $path, string $domainAlias = null);
}
```

The arguments received are easy to understand:

- `path`: this is the path of the current request
- `domainAlias`: this is the alias of the domain detected if found (null
otherwise). So this alias depend of the configuration under `seo_override.domains`.

Your fetcher should return a `Joli\SeoOverride\Seo` matching the path and
domain alias - null if no Seo found.

## Service definition

Once the fetcher is written, you need to **configure the related service**. You
need to add it the `seo_override.fetcher` **tag** to make our bundle aware of
your fetcher.

Our tag supports **two attributes**:

- `alias`: this attribute is **mandatory**. It defines the key to be used as
`type` under `seo_override.fetchers` configuration.

- `required_options`: this attribute is **optional**. It defines a list of
options (separated by commas) mandatory when configuring the fetcher under
`seo_override.fetchers` configuration.

An option is a key allowed in fetcher configuration that will be passed to the
fetcher constructor. To make our bundle understanding which constructor
argument should be mapped to which option, the option's name should be the
**snake_cased** name of the constructor argument's name.

## Example

Given the following fetcher:

```php
<?php

namespace App;

class MyFetcher implements Joli\SeoOverride\Fetcher
{
    public function __construct($firstOption, $secondOption, $optionalOption = 'foobar')
    {
    }
}
```

We define the following service:

```yaml
services:
    my_fetcher:
        class: App\MyFetcher
        tags:
            - { name: seo_override.fetcher, alias: my_fetcher, required_options: 'first_option,second_option' }
```

Then you will be able to configure your fetcher inside SeoOverride:

```yaml
seo_override:
    fetchers:
        - { type: my_fetcher, first_option: 'hello', second_option: 'world', optional_option: 'yolo' }
```
