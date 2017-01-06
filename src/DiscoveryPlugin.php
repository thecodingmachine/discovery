<?php

declare (strict_types = 1);

namespace TheCodingMachine\Discovery;

use Composer\Composer;
use Composer\EventDispatcher\EventSubscriberInterface;
use Composer\IO\IOInterface;
use Composer\Package\PackageInterface;
use Composer\Plugin\PluginInterface;
use Composer\Script\Event;
use Composer\Script\ScriptEvents;
use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use TheCodingMachine\Discovery\Utils\FileSystem;
use TheCodingMachine\Discovery\Utils\JsonException;

class DiscoveryPlugin implements PluginInterface, EventSubscriberInterface
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

        $discoveryPackages = $this->getDiscoveryPackages();
        $finalArray = $this->buildFinalArray($discoveryPackages);

        $fileSystem = new FileSystem();
        $fileSystem->dumpFile('.discovery/discovery_data.php', '<?php
return '.var_export($finalArray, true).";\n");

        // Let's copy the Discovery class in the .discovery directory. This is needed because otherwise, we have no way to "find" the .discovery directory easily.
        $fileSystem->dumpFile('.discovery/Discovery.php', file_get_contents(__DIR__.'/Discovery.php.tpl'));

        $this->registerClassInAutoloader();
    }

    /**
     * Returns the list of packages containing a "discovery.json" file in the root directory.
     *
     * Packages are ordered by dependencies.
     *
     * @return PackageInterface[]
     */
    private function getDiscoveryPackages()
    {
        $localRepos = $this->composer->getRepositoryManager()->getLocalRepository();
        $unorderedPackagesList = $localRepos->getPackages();

        $orderedPackageList = PackagesOrderer::reorderPackages($unorderedPackagesList);

        return array_filter($orderedPackageList, function (PackageInterface $package) {
            $installationManager = $this->composer->getInstallationManager();

            $packageInstallPath = $installationManager->getInstallPath($package);

            return file_exists($packageInstallPath.'/discovery.json');
        });
    }

    /**
     * Returns the parsed JSON of the discovery.json file of a package.
     *
     * @param PackageInterface $package
     *
     * @return array
     *
     * @throws \TheCodingMachine\Discovery\Utils\JsonException
     */
    private function getDiscoveryJson(PackageInterface $package) : array
    {
        $installationManager = $this->composer->getInstallationManager();
        $packageInstallPath = $installationManager->getInstallPath($package);

        $path = $packageInstallPath.'/discovery.json';

        $jsonParser = new JsonParser();
        try {
            $result = $jsonParser->parse(file_get_contents($path), JsonParser::PARSE_TO_ASSOC);
        } catch (ParsingException $e) {
            throw new JsonException(sprintf('Invalid JSON in file "%s": %s', $path, $e->getMessage()), 0, $e);
        }

        if (!is_array($result)) {
            throw new JsonException(sprintf('File "%s" should contain a JSON object.', $path));
        }

        return $result;
    }

    /**
     * Builds the array that will be exported in the generated TheCodingMachine\Discovery class.
     *
     * @param PackageInterface[] $discoveryPackages
     *
     * @return array
     */
    private function buildFinalArray(array $discoveryPackages) : array
    {
        $array = [];

        foreach ($discoveryPackages as $package) {
            $json = $this->getDiscoveryJson($package);

            foreach ($json as $key => $values) {
                $existingValues = $array[$key] ?? [];
                if (!is_array($values)) {
                    $values = [$values];
                }
                $existingValues = array_merge($existingValues, $values);
                $array[$key] = $existingValues;
            }
        }

        return $array;
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
}
