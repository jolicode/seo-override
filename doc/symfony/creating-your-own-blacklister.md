# Creating your own blacklister

Creating your own blacklister is really easy.

## The blacklister class

First, you need to **write a class** implementing the `Joli\SeoOverride\Bridge\Symfony\Blacklister`
interface. This interface defines only one method:

```php
interface Blacklister
{
    public function isBlacklisted(Request $request, Response $response): bool;
}
```

Your fetcher should return `true` to avoid running fetcher for the current
request, `false` otherwise.

## Service definition

Once the blacklister is written, you need to **configure the related service**.
You need to add it the `seo_override.blacklister` **tag** to make our bundle
aware of your blacklister.

Our tag supports **two attributes**:

- `alias`: this attribute is **mandatory**. It defines the key to be used as
`type` under `seo_override.blacklisters` configuration.

- `required_options`: this attribute is **optional**. It defines a list of
options (separated by commas) mandatory when configuring the blacklister under
`seo_override.blacklisters` configuration.

An option is a key allowed in blacklister configuration that will be passed to
the blacklister constructor. To make our bundle understanding which constructor
argument should be mapped to which option, the option's name should be the
**snake_cased** name of the constructor argument's name.

## Example

Given the following blacklister:

```php
<?php

namespace App;

class MyBlacklister implements Joli\SeoOverride\Bridge\Symfony\Blacklister
{
    public function __construct($firstOption, $secondOption, $optionalOption = 'foobar')
    {
    }

    //...
}
```

We define the following service:

```yaml
services:
    my_blacklister:
        class: App\MyBlacklister
        tags:
            - { name: seo_override.blacklister, alias: my_blacklister, required_options: 'first_option,second_option' }
```

Then you will be able to configure your blacklister inside SeoOverride:

```yaml
seo_override:
    blacklist:
        - { type: my_blacklister, first_option: 'hello', second_option: 'world', optional_option: 'yolo' }
```
