---
title: Passing additional meta-data
subTitle: 
currentMenu: metadata
---

Assets are represented by **strings**. Yet, if you need to pass more complex objects, you can add metadata to your value using the `meta` key.

<div class="text-center"><code>discovery.json</code></div>
```json
{
    "some_asset_type": [
        {
            "value": "some_value",
            "metadata": {
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
    $package = $asset->getPackage(); // The composer package this asset comes from
    $packageDir = $asset->getPackageDir(); // The directory of the package
    $priority = $asset->getPriority(); // The priority (if configured, see below)
}
```