---
title: Naming conventions
subTitle: 
currentMenu: conventions
---

This document is here to help you choose a name for your asset type.

<div class="alert alert-info">As a general rule, the <strong>consumer</strong> (i.e. the package consuming the assets) should decide the name of the asset type.</div>

Class assets
------------

If your assets are classes that implement a common interface, it is a good practice to use the name of the interface as an asset type.

For instance:

<div class="text-center"><code>discovery.json</code></div>
```json
{
    "Interop\\Container\\ServiceProviderInterface": "My\\ServiceProvider"
}
```

Here: the package is advertising that it contains a `My\ServiceProvider` class that implements the `Interop\Container\ServiceProviderInterface`. A container could use that information to discover all service providers implementing this interface.

<div class="alert alert-warning"><strong>Warning!</strong> The purpose of <code>thecodingmachine/discovery</code> is not to replace your favorite framework's dependency injection container! Most of the time, <strong>the dependency injection container from your framework can help you register a class provided by a package in your application.</strong> However, <code>thecodingmachine/discovery</code> can be very handy when you don't have a container <em>yet</em> (for instance it is very handy to provide service providers to the container when the container is still being built).</div>

Other assets
------------

If your assets are something else (maybe documentation files, or JS/CSS files, ...), we strongly recommend to prefix your assets name with your <strong>vendor name</strong>.

For instance:

<div class="text-center"><code>discovery.json</code></div>
```json
{
    "my_vendor_name/docs": [
        "doc/index.md",
        "doc/advanced.md"
    ]
}
```
