Discovery
=========

[![Latest Stable Version](https://poser.pugx.org/thecodingmachine/discovery/v/stable)](https://packagist.org/packages/thecodingmachine/discovery)
[![Total Downloads](https://poser.pugx.org/thecodingmachine/discovery/downloads)](https://packagist.org/packages/thecodingmachine/discovery)
[![Latest Unstable Version](https://poser.pugx.org/thecodingmachine/discovery/v/unstable)](https://packagist.org/packages/thecodingmachine/discovery)
[![License](https://poser.pugx.org/thecodingmachine/discovery/license)](https://packagist.org/packages/thecodingmachine/discovery)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/thecodingmachine/discovery/badges/quality-score.png?b=8.0)](https://scrutinizer-ci.com/g/thecodingmachine/discovery/?branch=8.0)
[![Build Status](https://travis-ci.org/thecodingmachine/discovery.svg?branch=8.0)](https://travis-ci.org/thecodingmachine/discovery)


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

What is the order for the assets?
---------------------------------

Assets are returned in an order respecting the dependencies between packages.

So if package B depends on package A, then assets of package A will be returned before assets of package B.