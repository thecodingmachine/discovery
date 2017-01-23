<?php


namespace TheCodingMachine\Discovery;

/**
 * Use this class to access data stored in the discovery.json files at the root of your packages.
 */
class Discovery implements DiscoveryInterface
{
    private static $instance;

    private $values;
    private $assetTypes;

    /**
     * Singleton. Noone creates this object directly.
     */
    private function __construct()
    {
        $this->values = require __DIR__.'/discovery_values.php';
        $this->assets = require __DIR__.'/discovery_asset_types.php';
    }

    /**
     * Returns the unique instance of this class (singleton).
     *
     * @return self
     */
    public static function getInstance(): self
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Returns the assets of the requested type.
     *
     * If no assets are found, an empty array is returned.
     *
     * @param string $assetType
     * @return string[]
     */
    public function get(string $assetType) : array
    {
        return $this->values[$assetType] ?? [];
    }
}
