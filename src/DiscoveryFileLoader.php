<?php


namespace TheCodingMachine\Discovery;

use Seld\JsonLint\JsonParser;
use Seld\JsonLint\ParsingException;
use TheCodingMachine\Discovery\Utils\IOException;
use TheCodingMachine\Discovery\Utils\JsonException;

/**
 * Class in charge of loading a discovery.json file.
 */
class DiscoveryFileLoader
{
    /**
     * Returns an array of array of asset operations indexed by asset type.
     *
     * @param \SplFileInfo $file
     * @return AssetOperation[][]
     * @throws JsonException
     */
    public function loadDiscoveryFile(\SplFileInfo $file, string $package, string $packageDir) : array
    {
        $jsonParser = new JsonParser();
        try {
            $result = $jsonParser->parse($this->getContents($file), JsonParser::PARSE_TO_ASSOC);
        } catch (ParsingException $e) {
            throw new JsonException(sprintf('Invalid JSON in file "%s": %s', $file->getPath(), $e->getMessage()), 0, $e);
        }

        if (!is_array($result) || !$this->isAssoc($result)) {
            throw new JsonException(sprintf('File "%s" should contain a JSON object.', $file->getPath()));
        }

        $assetTypes = [];

        foreach ($result as $type => $array) {
            if (!is_array($array)) {
                $array = [$array];
            }
            foreach ($array as $value) {
                $assetTypes[$type][] = $this->toAssetOperation($value, $package, $packageDir);
            }
        }
        return $assetTypes;
    }

    /**
     * Returns false if the array is numerically indexed, starting at 0.
     *
     * @param array $arr
     * @return bool
     */
    private function isAssoc(array $arr)
    {
        if (array() === $arr) {
            return true;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

    /**
     * Returns the contents of the file.
     *
     * @param \SplFileInfo $file
     * @return string the contents of the file
     *
     */
    private function getContents(\SplFileInfo $file)
    {
        $level = error_reporting(0);
        $content = file_get_contents($file->getPathname());
        error_reporting($level);
        if (false === $content) {
            $error = error_get_last();
            throw new IOException($error['message']);
        }
        return $content;
    }

    /**
     * Transforms a value string into a value array.
     * Also, if a value array is passed, checks it contains valid data.
     *
     * @param mixed $value
     * @return AssetOperation
     */
    private function toAssetOperation($value, string $package, string $packageDir) : AssetOperation
    {
        if (is_array($value)) {
            return AssetOperation::buildFromArray($value, $package, $packageDir);
        } else {
            return AssetOperation::buildFromString((string) $value, $package, $packageDir);
        }
    }

    /**
     * Saves the list of asset operations into a file.
     *
     * @param AssetOperation[][] $assetOperationTypes
     * @param \SplFileObject $file
     */
    public function saveDiscoveryFile(array $assetOperationTypes, \SplFileObject $file)
    {
        $simpleAssetOperationTypes = [];
        foreach ($assetOperationTypes as $assetType => $assetOperations) {
            $simpleAssetOperationTypes[$assetType] = $this->simplify($assetOperations);
        }

        $file->fwrite(json_encode($simpleAssetOperationTypes, JSON_PRETTY_PRINT));
    }

    /**
     * @param AssetOperation[] $assetOperations
     * @return array|string
     */
    private function simplify(array $assetOperations)
    {
        $simplifiedAssetOperations = array_map(function(AssetOperation $assetOperation) {
            return $assetOperation->toSimpleArray();
        }, $assetOperations);

        if (count($simplifiedAssetOperations) === 1) {
            return array_values($simplifiedAssetOperations)[0];
        } else {
            return $simplifiedAssetOperations;
        }
    }
}
