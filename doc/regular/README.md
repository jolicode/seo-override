# Regular usage

## Manager

The central class in SeoOverride is `Joli\SeoOverride\SeoManager`. It's the
**main class** you should use in your code.

```php
$fetchers = [
    //...
];
$domains = [];
$manager = new \Joli\SeoOverride\SeoManager($fetchers, $domains);
```

The manager provides 2 ways to override SEO for a page:

```php
$html = '<html>...</html>';
$path = '/';
$domain = 'example.org';

$manager->updateAndOverride($html, $path, $domain);
// or
$manager->updateSeo($path, $domain);
$manager->overrideHtml($html);
```

The two main configuration points are **fetchers** and **domains**.
Your SEO HTML should have a specific format to allow SeoOverride to update
them.

## HTML syntax

The manager overrides SEO in your html by replacing content wrapped in some
HTML comments. See the following example:

```html
<html>
    <head>
        <!--SEO_TITLE--><title>Your default title</title><!--/SEO_TITLE-->
        <!--SEO_DESCRIPTION--><meta name="description" content="your default description"><!--/SEO_DESCRIPTION-->
        <!--SEO_KEYWORDS--><meta name="keywords" content="your,keywords"><!--/SEO_KEYWORDS-->
        <!--SEO_ROBOTS--><meta name="robots" content="default_config"><!--/SEO_ROBOTS-->
        <!--SEO_CANONICAL--><link rel="canonical" href="/default_canonical_url"><!--/SEO_CANONICAL-->
        <!--SEO_OG_TITLE--><meta property="og:title" content="Your default og:title"><!--/SEO_OG_TITLE-->
        <!--SEO_OG_DESCRIPTION--><meta property="og:description" content="Your default og:description"><!--/SEO_OG_DESCRIPTION-->
    </head>
    <body></body>
</html>
```

These HTML comments make SeoOverride able to replace your content more safely
than if it should parse your HTML itself.

Note:
> If a SEO property is not present in your HTML or not wrapped by our special
> comments then it will simply not be overriden without SeoOverride throwing
> any error.

Note:
> The HTML comments will be automatically removed from the resulting HTML.

## Fetchers

A fetcher is responsible to retrieve a `Joli\SeoOverride\Seo` value object for
a given request (path and domain).

This package provides **3 built-in fetchers**:

```php
$fetchers = [
    new Joli\SeoOverride\Bridge\Doctrine\DoctrineFetcher($doctrineRegistry),
    new Joli\SeoOverride\Fetcher\InMemoryFetcher($seoOverrides),
    new Joli\SeoOverride\Fetcher\PhpFetcher($includePath),
];
```

Note:
> The **order** in the `$fetchers` array is important. It determines the
> **priority** - the first `Seo` to be returned from a fetcher will be used by
> the manager.

See [this documentation](available-fetchers.md) to learn more about the built-in
fetchers.

If your need your own fetcher, you can easily write it - it only needs to
implement the interface `Joli\SeoOverride\Fetcher`.

## Domains

Domains are fully **optional**. You can simply use SeoOverride without
configuring them.

Domains allow your application to handle **multiple host** for your overrides,
by restricting the override to some domains.

To configure a domain in the manager, you need to define an **alias** and a
**regexp pattern** that should match the host of the request. We choose to
implement regex instead of static domain to allow different domain to behave
similarly in case you want it. See the example below:
 
```php
$domains = [
    'main' => 'www\.my_company\.com',
    'account' => 'account\.my_company\.com',
    'shop => '[\w]+\.my_company\.com',
];
 ```

Note:
> The **order** in the `$domains` array is important. It determines the
> **priority** - the first domain to match will be used to retrieve `Seo`
> through fetchers.
