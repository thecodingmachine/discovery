<?php


namespace TheCodingMachine\Discovery;

/**
 * Use this class to access data stored in the discovery.json files at the root of your packages.
 */
class Discovery
{
    private static $instance;

    private $data;

    /**
     * Singleton. Noone creates this object directly.
     */
    private function __construct()
    {
        $this->data = file_get_contents(__DIR__.'/discovery_data.php');
    }

    public static function getInstance()
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
     * @return array
     */
    public function get(string $assetType) : array
    {
        return $this->data[$assetType] ?? [];
    }
}
