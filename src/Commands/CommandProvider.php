<?php


namespace TheCodingMachine\Discovery\Commands;

use Composer\Plugin\Capability\CommandProvider as CommandProviderCapability;

/**
 * List of all commands provided by this package.
 */
class CommandProvider implements CommandProviderCapability
{

    /**
     * Retrieves an array of commands
     *
     * @return \Composer\Command\BaseCommand[]
     */
    public function getCommands()
    {
        return [
            new ListAssetTypesCommand(),
            new DumpCommand(),
            new AddAssetCommand(),
            new RemoveAssetCommand(),
        ];
    }
}
