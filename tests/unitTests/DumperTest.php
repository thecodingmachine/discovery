<?php


namespace TheCodingMachine\Discovery;


class DumperTest extends AbstractDiscoveryTest
{
    public function testDumpDiscoveryFiles()
    {
        $dumper = new Dumper($this->getComposer(), $this->getIO());

        $dumper->dumpDiscoveryFiles();

        $this->assertFileExists('.discovery/Discovery.php');
        $this->assertFileExists('.discovery/discovery_asset_types.php');
        $this->assertFileExists('.discovery/discovery_values.php');

        unlink('.discovery/Discovery.php');
        unlink('.discovery/discovery_asset_types.php');
        unlink('.discovery/discovery_values.php');
        rmdir('.discovery');
    }
}
