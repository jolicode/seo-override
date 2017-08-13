# Available blacklisters

| type  | Description | option |
|---|---|---|
| not_2xx | Blacklist if the response status code is not 2xx | None |
| path | Blacklist if the request path matches the pattern | - "pattern": mandatory, the regex pattern to match |

## not_2xx

The `not_2xx` blacklister refuses response whose status code is >= 300. It does
not have option. You only need to register it:

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
