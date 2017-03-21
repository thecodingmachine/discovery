#!/bin/bash

set -e

# Lets copy the code in a subdirectory of the tests, otherwise Composer cannot require the package.
rsync -av .. fixtures/copy/ --exclude tests --exclude vendor

COMPOSER=composer-test.json composer update -v

#rm -rf fixtures/copy
