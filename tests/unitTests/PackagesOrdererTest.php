<?php


namespace TheCodingMachine\Discovery\Tests;

use Composer\Semver\Constraint\Constraint;
use Composer\Semver\VersionParser;
use Composer\Package\Link;
use PHPUnit\Framework\TestCase;
use TheCodingMachine\Discovery\PackagesOrderer;

class PackagesOrdererTest extends TestCase
{
    private static $parser;

    protected static function getVersionParser()
    {
        if (!self::$parser) {
            self::$parser = new VersionParser();
        }

        return self::$parser;
    }

    protected function getPackage($name, $version, $class = 'Composer\Package\Package')
    {
        $normVersion = self::getVersionParser()->normalize($version);

        return new $class($name, $normVersion, $version);
    }

    public function testOrderer()
    {
        $packageA = $this->getPackage('package/za', '1.0');
        $packageB = $this->getPackage('package/b', '1.0');
        $packageC = $this->getPackage('package/c', '1.0');
        /* @var $packageA \Composer\Package\Package */
        /* @var $packageB \Composer\Package\Package */
        /* @var $packageC \Composer\Package\Package */
        $packageB->setRequires([
            new Link('package/b', 'package/za', new Constraint('=', '*'))
        ]);
        $packageC->setRequires([
            new Link('package/c', 'package/b', new Constraint('=', '*'))
        ]);

        $result = PackagesOrderer::reorderPackages([$packageC, $packageB, $packageA]);
        $this->assertSame([$packageA, $packageB, $packageC], $result);
    }

    /**
     * Tests that unrelated packages are ordered alphabetically
     */
    public function testOrdererOnUnrelatedPackages()
    {
        $packageA = $this->getPackage('package/a', '1.0');
        $packageB = $this->getPackage('package/b', '1.0');
        $packageC = $this->getPackage('package/c', '1.0');
        /* @var $packageA \Composer\Package\Package */
        /* @var $packageB \Composer\Package\Package */
        /* @var $packageC \Composer\Package\Package */

        $result = PackagesOrderer::reorderPackages([$packageC, $packageB, $packageA]);
        $this->assertSame([$packageA, $packageB, $packageC], $result);
    }
}
