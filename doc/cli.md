---
title: Command line interface
subTitle: 
currentMenu: cli
---

Discovery comes with a CLI (command line interface).

You can use the CLI commands to:

- debug what is happening
- modify your local `discovery.json`
- hook third party tools (like Gulp or Webpack)

Actually, *discovery* is adding a set of commands to composer.

## Listing asset types
 
```bash
$ composer discovery:list
```

will output the list of asset types and the list of assets in each asset-type:

```
my-asset-type-1:
  assetA
  assetB
  assetC
my-asset-type-2:
  assetD
```

You can also ask for the assets of a given asset-type:

```bash
$ composer discovery:list my-asset-type-2
```

will output:

```
my-asset-type-2:
  assetD
```

Finally, if you need more data, you can use the JSON output (maybe you want to output the metadata associated to each asset or you want to integrate the output with a third party tool).

```bash
$ composer discovery:list --format=json
```

## Dumping the Discovery files

Discovery is "dumping" a set of files that enable extremely quick access to the assets. **If you change manually a `discovery.json` file in your project, you will need to regenerate those files.** You can do this with the `discovery:dump` command:

```bash
$ composer discovery:dump
```

## Adding an asset

You can add an asset to an asset type using the `discovery:add` command:

```bash
$ composer discovery:add "my-asset-type" "my-asset-value"
```

The asset is added to the `discovery.json` file at the root of your project.

You can also set the priority while adding an asset:

```bash
$ composer discovery:add "my-asset-type" "my-asset-value" --priority=42
```

Note: currently, discovery does not support adding metadata via the command line. You will need to edit the `discovery.json` file manually to add metadata to an asset.

## Removing an asset

You can remove an asset from a project using the `discovery:remove` command:

```bash
$ composer discovery:remove "my-asset-type" "my-asset-value"
```

The asset is removed from the `discovery.json` file at the root of your project or a "remove" action is added in the `discovery.json` file at the root of your project (if the asset is defined in a dependency).
