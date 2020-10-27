<?php


namespace TheCodingMachine\Discovery\Tests\Commands;


use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use TheCodingMachine\Discovery\Commands\CommandProvider;

class CommandProviderTest extends TestCase
{
    public function testCommandProvider()
    {
        $commandProvider = new CommandProvider();
        $commands = $commandProvider->getCommands();
        $this->assertContainsOnlyInstancesOf(Command::class, $commands);
    }
}
