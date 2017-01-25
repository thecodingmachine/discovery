<?php
namespace TheCodingMachine\Discovery;

/**
 * An asset type is essentially an array of Assets
 */
class ImmutableAssetType implements AssetTypeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Asset[]
     */
    private $assets = [];

    public function __construct(string $name, array $assets)
    {
        $this->name = $name;
        $this->assets = $assets;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Asset[]
     */
    public function getAssets() : array
    {
        return $this->assets;
    }

    /**
     * Creates an ImmutableAssetType from a PHP array.
     *
     * @param string $name
     * @param array $assetsArray
     * @return ImmutableAssetType
     */
    public static function fromArray(string $name, array $assetsArray) : ImmutableAssetType
    {
        $assets = array_map(function (array $assetArray) {
            return Asset::fromArray($assetArray);
        }, $assetsArray);
        return new self($name, $assets);
    }
}
