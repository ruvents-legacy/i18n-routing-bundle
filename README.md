# RUVENTS I18n Routing Bundle

## Description

This bundle hacks Symfony Framework Bundle's router and prefixes all routes with an optional `/{_locale}` section.

## Configuration

```yaml
ruvents_i18n_routing:
    locales: [ru, en]
    default_locale: ru
```

## Disable prefixing

To prevent routes from being prefixed, set `i18n` option to false. For example:

```yaml
# app/config/routing_dev.yml
_profiler:
    resource: "@WebProfilerBundle/Resources/config/routing/profiler.xml"
    prefix:   /_profiler
    options:  { i18n: false }
```
