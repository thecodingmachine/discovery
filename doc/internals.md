---
title: Internals and performance
subTitle: 
currentMenu: internals
---

Internals
---------

Internally, `thecodingmachine/discovery` is a **Composer plugin**. The plugin is triggered each time a `composer install` or `composer update` command is run.

When the plugin is triggered, all data stored in `discovery.json` files is automatically exported to a set of PHP files.

If you are wondering, by default, the data is stored in the `.discovery` directory, at the root of your project. You can put the directory in your `.gitignore` as the directory will be automatically regenerated on every `composer install`.


Performances
------------

What about performances? They are excellent, thanks for asking!
Since data from `discovery.json` files is exported in PHP files, there is no loading / scanning of `discovery.json` files done at runtime. Everything lives in opcache and is blazing fast to access.

A side-effect of this behaviour is that if you edit a `discovery.json` file, you won't see a change in your application. You will need to run `composer install` or `composer update` first to "rebuild" the `.discovery` directory.
