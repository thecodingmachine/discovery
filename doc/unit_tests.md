---
title: Running unit tests
subTitle: 
currentMenu: unit
---

Unit/integration tests are run in 2 steps.

Running integration tests
-------------------------

First step: execute the plugin in Composer

```
cd tests/
./run.sh
```

This will run the **integration** tests (i.e. this will run the Composer plugin for real, inside Composer)

Running unit tests
------------------

Second step: run unit tests

```
cd ..
vendor/bin/phpunit
```

Note: you must run the integration tests **before** the unit tests. Indeed, the integration tests are generating PHP files and classes that will be later tested by the unit tests.