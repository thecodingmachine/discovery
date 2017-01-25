<?php
namespace TheCodingMachine\Discovery;

/**
 * An asset type is essentially an array of Assets
 */
class AssetType implements \JsonSerializable, AssetTypeInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var Asset[]
     */
    private $assets = [];

    private $sorted = false;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Registers an asset operation.
     * Note: it is expected that asset operations will be added in the order of package dependencies.
     * Order of assets with same priority will be preserved.
     *
     * @param AssetOperation $operation
     */
    public function addAssetOperation(AssetOperation $operation)
    {
        if ($operation->getOperation() === AssetOperation::ADD) {
            $this->assets[] = $operation->getAsset();
        } else {
            // This is a remove!
            $this->assets = array_values(array_filter($this->assets, function (Asset $asset) use ($operation) {
                return $asset->getValue() !== $operation->getAsset()->getValue();
            }));
        }

        $this->sorted = false;
    }

    /**
     * Sort assets by priority.
     */
    private function sortAssets()
    {
        if ($this->sorted === true) {
            return;
        }

        // Let's order assets by priority.
        $this->stableSort($this->assets, function (Asset $asset1, Asset $asset2) {
            return $asset2->getPriority() <=> $asset1->getPriority();
        });

        $this->sorted = true;
    }

    /**
     * Similar to a usort except that if 2 priorities are equal, order is preserved.
     * Shamelessly copied from http://php.net/manual/fr/function.usort.php#38827
     *
     * @param $array
     * @param string $cmp_function
     */
    private function stableSort(&$array, $cmp_function = 'strcmp')
    {
        // Arrays of size < 2 require no action.
        if (count($array) < 2) {
            return;
        }
        // Split the array in half
        $halfway = count($array) / 2;
        $array1 = array_slice($array, 0, $halfway);
        $array2 = array_slice($array, $halfway);
        // Recurse to sort the two halves
        $this->stableSort($array1, $cmp_function);
        $this->stableSort($array2, $cmp_function);
        // If all of $array1 is <= all of $array2, just append them.
        if (call_user_func($cmp_function, end($array1), $array2[0]) < 1) {
            $array = array_merge($array1, $array2);
            return;
        }
        // Merge the two sorted arrays into a single sorted array
        $array = array();
        $ptr1 = $ptr2 = 0;
        while ($ptr1 < count($array1) && $ptr2 < count($array2)) {
            if (call_user_func($cmp_function, $array1[$ptr1], $array2[$ptr2]) < 1) {
                $array[] = $array1[$ptr1++];
            } else {
                $array[] = $array2[$ptr2++];
            }
        }
        // Merge the remainder
        while ($ptr1 < count($array1)) {
            $array[] = $array1[$ptr1++];
        }
        while ($ptr2 < count($array2)) {
            $array[] = $array2[$ptr2++];
        }
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
        $this->sortAssets();
        return $this->assets;
    }

    /**
     * Shortcut to get an array of values of the assets.
     *
     * @return string[]
     */
    public function getValues() : array
    {
        return array_map(function (Asset $asset) {
            return $asset->getValue();
        }, $this->getAssets());
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
        return array_map(function (Asset $asset) {
            return $asset->jsonSerialize();
        }, $this->getAssets());
    }
}
