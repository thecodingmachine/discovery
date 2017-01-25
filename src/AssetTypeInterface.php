<?php
namespace TheCodingMachine\Discovery;

/**
 * An asset type is essentially an array of Assets
 */
interface AssetTypeInterface
{
    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return Asset[]
     */
    public function getAssets(): array;
}
