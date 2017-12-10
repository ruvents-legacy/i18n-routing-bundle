[![Packagist](https://img.shields.io/packagist/l/ruvents/i18n-routing-bundle.svg)](LICENSE)
[![PHP from Packagist](https://img.shields.io/packagist/php-v/ruvents/i18n-routing-bundle.svg)](https://packagist.org/packages/ruvents/i18n-routing-bundle)
[![Packagist](https://img.shields.io/packagist/v/ruvents/i18n-routing-bundle.svg)](https://packagist.org/packages/ruvents/i18n-routing-bundle)
[![Packagist](https://img.shields.io/packagist/dt/ruvents/i18n-routing-bundle.svg)](https://packagist.org/packages/ruvents/i18n-routing-bundle)

[![Build Status](https://travis-ci.org/ruvents/i18n-routing-bundle.svg?branch=master)](https://travis-ci.org/ruvents/i18n-routing-bundle)
[![StyleCI](https://styleci.io/repos/80287657/shield?branch=master)](https://styleci.io/repos/80287657)

# RUVENTS I18n Routing Bundle

## Description

This bundle hacks Symfony Framework Bundle's router and prefixes all routes with an optional `/{_locale}` section.

## Configuration

```yaml
services:
    router: "@ruvents_i18n_routing.framework_router_decorator"

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
