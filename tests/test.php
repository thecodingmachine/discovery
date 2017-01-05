<?php
require_once __DIR__.'/vendor/autoload.php';

use TheCodingMachine\Discovery\Discovery;

$result = Discovery::getInstance()->get('test-asset');

assert($result === [
        'test-asset' =>
            [
                0 => 'a1',
                1 => 'a2',
                2 => 'b',
            ],
    ]);

$result = Discovery::getInstance()->get('no-exist');

assert($result === []);

echo "Tests passed successfully\n";
