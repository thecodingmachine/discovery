<?php
require_once __DIR__.'/vendor/autoload.php';

use TheCodingMachine\Discovery\Discovery;

$result = Discovery::getInstance()->get('test-asset');

if ($result !== [ 'a1', 'a2', 'b' ]) {
    throw new \Exception('Failed asserting returned assets. Returned: '.var_export($result, true));
}

$result = Discovery::getInstance()->get('no-exist');

if ($result !== []) {
    throw new \Exception('Failed asserting that non existent asset type returns empty array.');
}

echo "Tests passed successfully\n";
