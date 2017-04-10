---
title: discovery.json schema
subTitle: Complete syntax
currentMenu: schema
---

## **discovery.json** full syntax

```json
{
    "some_asset_type": [
        {
            "value": "some_value",
            "metadata": {
                "some": "metadata",
                "more": "metadata"
            },
            "action": "add|remove",
            "priority": 99
        }
    ]
}
```

A `discovery.json` file contains an array of asset types, indexed by name.

Each asset type contains an array of **asset objects**.

## Asset objects syntax

Each asset object can be represented this way:

```json
{
    "value": "some_value",
    "metadata": {
        "some": "metadata",
        "more": "metadata"
    },
    "action": "add|remove",
    "priority": 99
}
```

- **value** (*compulsory*, *string*): this is the value of the asset.
- **meta** (*optional*, defaults to `{}`): an array of metadata attached to the asset.
- **action** (*either 'add' or 'remove'*, defaults to 'add'): if set to remove, assets with the same value are actually removed from the asset type.
  This is useful to remove an asset that has been added by another package.
- **priority** (*optional*, *float*, defaults to `0`): The priority of the asset. Assets with higher priorities are returned first.

## Simple syntax

An asset object can be represented by a simple string.

So

```json
{
    "some_asset_type": [
        {
            "value": "some_value"
        }
    ]
}
```

can be simplified into:

```json
{
    "some_asset_type": [
        "some_value"
    ]
}
```

Furthermore, if the asset type array contains only one value, you can skip the array and simply provide the value:

```json
{
    "some_asset_type": "some_value"
}
```
