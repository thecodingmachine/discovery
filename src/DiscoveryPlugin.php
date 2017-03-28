<?php

declare(strict_types=1);

namespace TheCodingMachine\Discovery;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\Factory;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\Capability\CommandProvider;
use Composer\Plugin\Capable;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Symfony\Component\Filesystem\Filesystem;
use TheCodingMachine\Discovery\Commands\CommandProvider as DiscoveryCommandProvider;
use TheCodingMachine\Discovery\Commands\DumpCommand;

class DiscoveryPlugin implements PluginInterface, EventSubscriberInterface, Capable
{
    /**
     * @var Composer
     */
    protected $composer;
    protected $io;

    /**
     * Apply plugin modifications to Composer.
     *
     * @param Composer    $composer
     * @param IOInterface $io
     */
    public function activate(Composer $composer, IOInterface $io)
    {
        $this->composer = $composer;
        $this->io = $io;
    }

    public static function getSubscribedEvents()
    {
        return [
            ScriptEvents::PRE_AUTOLOAD_DUMP => 'beforeDumpAutoload',
        ];
    }

    public function beforeDumpAutoload(Event $event)
    {
        // Plugin has been uninstalled
        if (!file_exists(__FILE__)) {
            return;
        }

        $dumper = new Dumper($this->composer, $this->io);
        $dumper->dumpDiscoveryFiles();

        $this->registerClassInAutoloader();
    }

    /**
     * This registers the generated TheCodingMachine\Discovery class in the autoloader.
     */
    private function registerClassInAutoloader()
    {
        // Let's dynamically add a Discovery file to the autoloader
        $discoveryFile = '.discovery/Discovery.php';
        $autoload = $this->composer->getPackage()->getAutoload();
        $autoload['classmap'][] = $discoveryFile;
        $this->composer->getPackage()->setAutoload($autoload);
    }

    /**
     * Method by which a Plugin announces its API implementations, through an array
     * with a special structure.
     *
     * The key must be a string, representing a fully qualified class/interface name
     * which Composer Plugin API exposes.
     * The value must be a string as well, representing the fully qualified class name
     * of the implementing class.
     *
     * @tutorial
     *
     * return array(
     *     'Composer\Plugin\Capability\CommandProvider' => 'My\CommandProvider',
     *     'Composer\Plugin\Capability\Validator'       => 'My\Validator',
     * );
     *
     * @return string[]
     */
    public function getCapabilities()
    {
        return [
            CommandProvider::class => DiscoveryCommandProvider::class,
        ];
    }
}
