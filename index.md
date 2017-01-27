Discovery
=========

[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/discovery/v/stable)](https://packagist.org/packages/thecodingmachine/discovery)
[![Total Downloads](https://poser.pugx.org/thecodingmachine/discovery/downloads)](https://packagist.org/packages/thecodingmachine/discovery)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/discovery/v/unstable)](https://packagist.org/packages/thecodingmachine/discovery)
[![License](https://poser.pugx.org/thecodingmachine/discovery/license)](https://packagist.org/packages/thecodingmachine/discovery)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/discovery/badges/quality-score.png?b=1.1)](https://scrutinizer-ci.com/g/thecodingmachine/discovery/?branch=1.1)
[![Build Status](https://travis-ci.org/thecodingmachine/discovery.svg?branch=1.1)](https://travis-ci.org/thecodingmachine/discovery)
[![Coverage Status](https://coveralls.io/repos/thecodingmachine/discovery/badge.svg?branch=1.1&service=github)](https://coveralls.io/github/thecodingmachine/discovery?branch=1.1)

Publish and discover assets in your PHP projects

This package helps you find "static" assets in your Composer packages.

What problem does this solve?
-----------------------------

For your project, you are looking to search through all the installed packages for some kind of resource. It could be:

- all the classes implementing some interface
- a set of config files scattered in some packages
- JS or CSS files
- ...

We offer a simple solution to solve this problem:

<div class="text-center">
<img src="doc/img/discovery.png" alt="" />
</div>

<br/>

- Packages **publishing** assets add a `discovery.json` file at the root of the package. This file contains the list of *published assets*.
- you include `thecodingmachine/discovery` in your project
- you use the `TheCodingMachine\Discovery` class to explore the available assets

discovery.json format
-----------------------

In packages *publishing* assets, put a `discovery.json` with the following format:

```json
{
    "some_asset_type": [
        "some_value",
        "another_value"
    ]
}
```

In the example above, `some_asset_type` is an *asset type*. You will query on that identifier in your PHP code.

Want to view the full syntax of `discovery.json`? Check out [the discovery.json schema document](doc/discovery_schema.md)

Discovering assets
------------------

From your project, simply run:

```php
$assets = TheCodingMachine\Discovery::getInstance()->get('some_asset_type');

// This will scan all discovery.json files and returns an array of values.
```

Install
-------

Simply run:

```sh
composer require thecodingmachine/discovery
```

More documentation
------------------

<div class="row">
    <div class="col-xs-12 col-sm-6">
        <a href="doc/metadata.html" class="btn btn-primary btn-large btn-block">Adding metadata</a>
    </div>
    <div class="col-xs-12 col-sm-6">
        <a href="doc/remove.html" class="btn btn-primary btn-large btn-block">Removing assets</a>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <a href="doc/order.html" class="btn btn-primary btn-large btn-block">Managing assets order</a>
    </div>
    <div class="col-xs-12 col-sm-6">
        <a href="doc/conventions.html" class="btn btn-primary btn-large btn-block">Naming conventions</a>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <a href="doc/internals.html" class="btn btn-primary btn-large btn-block">Internals and performance</a>
    </div>
    <div class="col-xs-12 col-sm-6">
        <a href="doc/discovery_schema.html" class="btn btn-primary btn-large btn-block">discovery.json schema</a>
    </div>
</div>
<div class="row">
    <div class="col-xs-12 col-sm-6">
        <a href="doc/unit_tests.html" class="btn btn-info btn-large btn-block">Running unit tests</a>
    </div>
    <div class="col-xs-12 col-sm-6">
        <a href="doc/alternatives.html" class="btn btn-info btn-large btn-block">Alternatives</a>
    </div>
</div>
