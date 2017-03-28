<?php


namespace TheCodingMachine\Discovery;

/**
 * Represent an asset.
 * Returned by the "getAssets" method.
 */
class Asset implements \JsonSerializable
{
    /**
     * @var string
     */
    private $value;
    /**
     * @var string
     */
    private $package;
    /**
     * @var string
     */
    private $packageDir;
    /**
     * @var float
     */
    private $priority;

    /**
     * @var mixed
     */
    private $metadata;

    public function __construct(string $value, string $package, string $packageDir, float $priority, $metadata)
    {
        $this->value = $value;
        $this->package = $package;
        $this->packageDir = $packageDir;
        $this->priority = $priority;
        $this->metadata = $metadata;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * Returns the name of the Composer package.
     *
     * @return string
     */
    public function getPackage(): string
    {
        return $this->package;
    }

    /**
     * Returns the package directory, relative to root directory.
     *
     * @return string
     */
    public function getPackageDir(): string
    {
        return $this->packageDir;
    }

    /**
     * The higher the priority the earlier the asset in the array.
     *
     * @return float
     */
    public function getPriority(): float
    {
        return $this->priority;
    }

    /**
     * @return mixed
     */
    public function getMetadata()
    {
        return $this->metadata;
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    public function jsonSerialize()
    {
        return [
            'value' => $this->getValue(),
            'package' => $this->getPackage(),
            'packageDir' => $this->getPackageDir(),
            'priority' => $this->getPriority(),
            'metadata' => $this->getMetadata()
        ];
    }

    public static function fromArray(array $array) : Asset
    {
        return new self($array['value'], $array['package'], $array['packageDir'], $array['priority'], $array['metadata']);
    }

    /**
     * Returns a simplified array representing the object.
     *
     * @return array|string
     */
    public function toSimpleArray()
    {
        $item = [
            'value' => $this->getValue(),
        ];

        if ($this->getPriority() !== 0.0) {
            $item['priority'] = $this->getPriority();
        }

        if (!empty($this->getMetadata())) {
            $item['metadata'] = $this->getMetadata();
        }

        if (count($item) === 1) {
            return $item['value'];
        } else {
            return $item;
        }
    }
}
