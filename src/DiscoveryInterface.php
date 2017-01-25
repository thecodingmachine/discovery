<?php

declare(strict_types=1);

namespace TheCodingMachine\Discovery;

/**
 * Discover static assets in your PHP project.
 */
interface DiscoveryInterface
{
    /**
     * Returns the assets of the requested type.
     *
     * If no assets are found, an empty array is returned.
     *
     * @param string $assetType
     *
     * @return array
     */
    public function get(string $assetType) : array;
}
