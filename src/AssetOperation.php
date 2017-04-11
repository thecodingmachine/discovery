<?php


namespace TheCodingMachine\Discovery;

use TheCodingMachine\Discovery\Utils\JsonException;

/**
 * Represents an operation on an asset.
 * Either "add" or "remove".
 */
class AssetOperation
{
    const ADD = 'add';
    const REMOVE = 'remove';

    /**
     * @var string
     */
    private $operation;
    /**
     * @var Asset
     */
    private $asset;

    /**
     * @param string $operation Either "add" or "remove"
     * @param Asset $asset
     * @throws InvalidArgumentException
     */
    public function __construct(string $operation, Asset $asset)
    {
        if ($operation !== 'add' && $operation !== 'remove') {
            throw new InvalidArgumentException('The action can only be add or remove.');
        }
        $this->operation = $operation;
        $this->asset = $asset;
    }

    /**
     * @return string
     */
    public function getOperation(): string
    {
        return $this->operation;
    }

    /**
     * @return Asset
     */
    public function getAsset(): Asset
    {
        return $this->asset;
    }

    /**
     * Builds the AssetOperation from a simple string.
     *
     * @param string $value
     * @param string $package
     * @return AssetOperation
     */
    public static function buildFromString(string $value, string $package, string $packageDir) : AssetOperation
    {
        return new self(self::ADD, new Asset($value, $package, $packageDir, 0, []));
    }

    /**
     * Builds the AssetOperation from an array.
     *
     * @param array $array
     * @param string $package
     * @return AssetOperation
     * @throws JsonException
     */
    public static function buildFromArray(array $array, string $package, string $packageDir) : AssetOperation
    {
        if (!isset($array['value'])) {
            throw new JsonException(sprintf('Missing "value" key in discovery.json from package %s', $package));
        }
        $value = $array['value'];
        unset($array['value']);

        if (isset($array['priority'])) {
            $priority = $array['priority'];
            unset($array['priority']);
        } else {
            $priority = 0.0;
        }

        if (isset($array['metadata'])) {
            $metadata = $array['metadata'];
            unset($array['metadata']);
        } else {
            $metadata = [];
        }

        if (!empty($array)) {
            throw new JsonException(sprintf('Unexpected key(s) in discovery.json from package %s: "%s"', $package, implode(', ', array_keys($array))));
        }

        return new self(self::ADD, new Asset($value, $package, $packageDir, $priority, $metadata));
    }

    /**
     * Returns a simplified array/string representing this asset operation.
     *
     * @return array|string
     */
    public function toSimpleArray()
    {
        $simple = $this->asset->toSimpleArray();
        if ($this->operation === self::REMOVE) {
            if (is_string($simple)) {
                $simple = [ 'value' => $simple ];
            }
            $simple['action'] = self::REMOVE;
        }
        return $simple;
    }
}
