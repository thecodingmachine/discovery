<?php


namespace TheCodingMachine\Discovery;

/**
 * Use this class to access data stored in the discovery.json files at the root of your packages.
 */
class Discovery implements DiscoveryInterface
{
    private static $instance;

    /**
     * @var string[]
     */
    private $values;
    /**
     * @var AssetType[]
     */
    private $assetTypes;
    /**
     * @var array[]
     */
    private $assetTypesArray;

    /**
     * Singleton. Noone creates this object directly.
     */
    private function __construct()
    {
        $this->values = require __DIR__.'/discovery_values.php';
        $this->assetTypesArray = require __DIR__.'/discovery_asset_types.php';
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
     * Returns the asset values of the requested type.
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

    /**
     * Returns an asset type object for the requested type.
     *
     * If no assets are found, an AssetType object containing no assets is returned.
     *
     * @param string $assetType
     * @return AssetTypeInterface
     */
    public function getAssetType(string $assetType) : AssetTypeInterface
    {
        if (!isset($this->assetTypes[$assetType])) {
            if (isset($this->assetTypesArray[$assetType])) {
                $this->assetTypes[$assetType] = ImmutableAssetType::fromArray($assetType, $this->assetTypesArray[$assetType]);
            } else {
                $this->assetTypes[$assetType] = ImmutableAssetType::fromArray($assetType, []);
            }
        }
        return $this->assetTypes[$assetType];
    }
}
