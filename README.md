Discovery
=========

[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/discovery/v/stable)](https://packagist.org/packages/thecodingmachine/discovery)
[![Total Downloads](https://poser.pugx.org/thecodingmachine/discovery/downloads)](https://packagist.org/packages/thecodingmachine/discovery)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/discovery/v/unstable)](https://packagist.org/packages/thecodingmachine/discovery)
[![License](https://poser.pugx.org/thecodingmachine/discovery/license)](https://packagist.org/packages/thecodingmachine/discovery)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/discovery/badges/quality-score.png?b=1.1)](https://scrutinizer-ci.com/g/thecodingmachine/discovery/?branch=1.1)
[![Build Status](https://travis-ci.org/thecodingmachine/discovery.svg?branch=1.1)](https://travis-ci.org/thecodingmachine/discovery)


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

- in the packages "publishing" assets, you add a `discovery.json` file at the root of the package that contains a list of assets available in the package.
- you include `thecodingmachine/discovery` in your project
- you use the `TheCodingMachine\Discovery` class to explore the available assets

`discovery.json` format
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

Discovering assets
------------------

From your project, simply run:

```php
$assets = TheCodingMachine\Discovery::getInstance()->get('some_asset_type');

// This will scan all discovery.json files and returns an array of values.
```

Each individual value MUST be a string.

Install
-------

Simply run:

```sh
composer require thecodingmachine/discovery
```

Conventions
-----------

If your assets are classes that implement a common interface, it is a good practice to use the name of the interface as an asset type.

For instance:

```json
{
    "Interop\\Container\\ServiceProviderInterface": [
        "My\\ServiceProvider"
    ]
}
```

Here: the package is advertising that is contains a `My\ServiceProvider` class that implements the `Interop\Container\ServiceProviderInterface`. A container could use that information to discover all service providers implementing this interface.

What about performances?
------------------------

They are excellent, thanks for asking! All data stored in `discovery.json` files is automatically exported in a PHP file when you perform a "composer install" or "composer update" command. So there is no loading / scanning of `discovery.json` files done at runtime. Everything lives in opcache and is blazing fast to access.

If you are wondering, by default, the data is stored in the `.discovery` directory, at the root of your project. You can put the directory in your `.gitignore` if your want as the directory will be automatically regenerated on every `composer install`.


Passing additional meta-data
----------------------------

Stored values are strings. Yet, if you need to pass more complex objects, you can add metadata to your value using the `meta` key.

```json
{
    "some_asset_type": [
        {
            "value": "some_value",
            "meta": {
                "some": "metadata",
                "more": "metadata"
            }
        }
    ]
}
```

Notice in this example that the value passed is no longer a string, it is an object containing the `value` key (that contains the actual value) and the `meta` key (that contains the metadata).

Metadata can be queried using the `getAssets()` method that will return complete `Asset` objects:

```php
$assetType = TheCodingMachine\Discovery::getInstance()->getAssetType('some_asset_type');

foreach ($assetType->getAssets() as $asset) {
    $value = $asset->getValue();
    $meta = $asset->getMetadata();
    $package = $asset->getPackage(); // The name of the composer package this asset comes from
    $packageDir = $asset->getPackageDir(); // The directory of the package
    $priority = $asset->getPriority(); // The priority (if configured, see below)
}
```

Removing an asset added by another package
------------------------------------------

A package "B" can remove the assets added by another package "A" (assuming that package "B" requires "A").

To do so, use the `action` key:

```json
{
    "some_asset_type": [
        {
            "value": "some_value",
            "action": "remove"
        }
    ]
}
```

This will remove "some_value" from the asset type "some_asset_type".

What is the order for the assets?
---------------------------------

By default, assets are returned in an order respecting the dependencies between packages.

So if package B depends on package A, then assets of package A will be returned before assets of package B.

You can however alter this order using the `priority` modifier.

```json
{
    "some_asset_type": [
        {
            "value": "some_value",
            "priority": 99
        }
    ]
}
```

The default priority is 0.
Values with higher priorities will come first in the array.
Values with the same priority are ordered by package dependency order.

Other projects providing discovery features
-------------------------------------------

Other packages have already tried to tackle the problem of static assets. In particular:

- [Puli](http://docs.puli.io/en/latest/discovery/introduction.html): a much more complex/complete solution that also features discovery capabilities
- [soundasleep/component-discovery](https://github.com/soundasleep/component-discovery): seems abandoned

### How does this project compares to Puli?

We initially started using Puli's discovery features. We ran into a number of problems that triggered the development of this package.

Puli is clearly a much more complete solution, that addresses discovery of classes as well as discovery of files natively.

Puli is more strict. Packages publishing assets need to include a package that "declare" an asset type before being able to publish the asset.

Puli is a complex project with more dependencies. It features many packages with many dependencies. Those dependencies can sometime be in conflict with your project. For instance, Puli uses ramsey/uuid 2.0 while Laravel 5.3 uses ramsey/uuid 3.0, making both incompatible. By comparison, thecodingmachine/discovery has no dependencies.

Puli is independent from Composer. This has pros and cons. As such, when assets are imported, it has no way to order them "by dependency", which is often what the user wants.

Running unit tests
------------------

Unit/integration tests are run in 2 steps.

First step: execute the plugin in Composer

```
cd tests/
./run.sh
```

Second step: run unit tests

```
cd ..
vendor/bin/phpunit
```
