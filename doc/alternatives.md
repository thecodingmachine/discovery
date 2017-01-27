---
title: Alternatives
subTitle: Other projects providing discovery features
currentMenu: alternatives
---

Other packages have already tried to tackle the problem of static assets. In particular:

- [Puli](http://docs.puli.io/en/latest/discovery/introduction.html): a much more complex/complete solution that also features discovery capabilities
- [soundasleep/component-discovery](https://github.com/soundasleep/component-discovery): seems abandoned

## How does this thecodingmachine/discovery compares to Puli?

We initially started using Puli's discovery features. We ran into a number of problems that triggered the development of this package.

- Puli is clearly a much more complete solution, that addresses discovery of classes as well as discovery of files natively.
- Puli is more strict. Packages publishing assets need to include a package that "declare" an asset type before being able to publish the asset.
- Puli is a complex project with more dependencies. It features many packages with many dependencies. Those dependencies can sometime be in conflict with your project. For instance, Puli uses ramsey/uuid 2.0 while Laravel 5.3 uses ramsey/uuid 3.0, making both incompatible. By comparison, thecodingmachine/discovery has no dependencies.
- Puli is independent from Composer. This has pros and cons. As such, when assets are imported, it has no way to order them "by dependency", which is often what the user wants.
