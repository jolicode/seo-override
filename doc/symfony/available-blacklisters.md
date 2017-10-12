# Available blacklisters

| type  | Description | option |
|---|---|---|
| not_2xx | Blacklist if the response status code is not 2xx | None |
| path | Blacklist if the request path matches the pattern | - "pattern": mandatory, the regex pattern to match |
| not_method | Blacklist if the request HTTP method is in the accepted ones | - "method": mandatory, the HTTP method(s) to accept |
| xml_http | Blacklist if the request is made through XmlHttpRequest | None |

## not_2xx

The `not_2xx` blacklister refuses response whose status code is >= 300 or < 200. It does
not have any option. You only need to register it:

```yaml
seo_override:
    blacklist:
        - { type: not_2xx }
```

Our bundle also supports a short syntax for blacklister with no option needed:

```yaml
seo_override:
    blacklist:
        - not_2xx
```

## path

The `path` blacklister refuses request whose path info matches the configured
`pattern` option. This option should be a valid regex pattern:

```yaml
seo_override:
    blacklist:
        -
            type: path
            pattern: '^/(admin|account)'
```

## not_method

The `not_method` blacklister refuses request whose method is not in the
configured `method` option. This option can be a single HTTP method or an array
of methods:

```yaml
seo_override:
    blacklist:
        -
            type: not_method
            method: [GET, POST]
            # if only one method should be accepted
            # method: GET
```

## xml_http

The `xml_http` blacklister refuses request which have been made through Ajax
(XmlHttpRequest). This blacklister does not have any option so the short YAML
syntax can be used:

```yaml
seo_override:
    blacklist:
        - xml_http
```
