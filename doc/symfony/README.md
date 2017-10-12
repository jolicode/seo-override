# Usage inside Symfony

## Installation

Register the provided bundle inside your kernel:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [
            new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            //...
            new \Joli\SeoOverride\Bridge\Symfony\SeoOverrideBundle(),
        ];

        return $bundles;
    }
}
```

## Configuration

### HTML syntax

To make SeoOverride able to override your SEO, you will need to wrap it in our
special HTML comments:

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

See the chapter [HTML syntax](../regular/README.md#html-syntax) to get more
information about this syntax.

### Fetchers

A fetcher is responsible to retrieve a `Joli\SeoOverride\Seo` value object for
a given request (path and domain).

There is some built-in fetchers that you can enable/configure via the config
`fetchers`:

```yaml
seo_override:
    fetchers:
        - { type: doctrine }
```

See [this documentation](available-fetchers.md) to learn more about the
built-in fetchers and how to configure them.

You need your own fetcher? Check out [this documentation](creating-your-own-fetcher.md).

Note:
> You can enable/configure different fetchers but the **order** is
> **important**. The first `Seo` to be returned from a fetcher will be used by
> the manager.

## Domains

Domains are fully **optional**. You can simply use SeoOverride without
configuring them.

Domains allow your application to handle **multiple host** for your overrides,
by restricting the override to some domains.

To configure a domain in SeoOverride, you need to define an **alias** and a
**regexp pattern** that should match the host of the request.
 
```yaml
seo_override:
    domains:
        main: 'www\.my_company\.com'
        account: 'account\.my_company\.com'
        shop: '[\w]+\.my_company\.com'
```

Note:
> The **order** in the `$domains` array is important. It determines the
> **priority** - the first domain to match will be used to retrieve `Seo`
> through fetchers.


### Encoding

You can setup the encoding that should be used when overriding the HTML markup.

```yaml
seo_override:
    encoding: KOI8-R # Default is UTF-8
```

Note:
> Internally the manager uses the `htmlspecialchars` function. Check out
> [the documentation](http://php.net/manual/en/function.htmlspecialchars.php#refsect1-function.htmlspecialchars-parameters)
> of the `$encoding` parameter to know which encoding is supported.

### Blacklist

You can blacklist some request/response to avoid fetcher to run (f.e. on non
2xx response, on your admin, on XHR calls, etc).

There is some built-in blacklister that you can enable/configure via the config
`blacklist`:

```yaml
seo_override:
    blacklist:
        - not_2xx
        - { type: path, pattern: '^/(admin|account)' }
```

By default, only the `not_2xx` blacklister is used.

You can **disable blacklist** behaviour (so fetchers are always called) by
setting the config to `false``:

```yaml
seo_override:
    blacklist: false
```

See [this documentation](available-blacklisters.md) to learn more about the
built-in blacklisters and how to configure them.

You need your own blacklister? Check out [this documentation](creating-your-own-blacklister.md).
