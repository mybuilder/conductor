<?php

namespace MyBuilder\Conductor\Command;

use Herrera\Phar\Update\Manager;
use Herrera\Phar\Update\Manifest;
use Herrera\Version\Parser;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdateCommand extends Command
{
    const MANIFEST_FILE = 'http://mybuilder.github.io/conductor/manifest.json';

    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(array('selfupdate'))
            ->setDescription('Updates conductor.phar to the latest version.');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $manifest = Manifest::loadFile(self::MANIFEST_FILE);
        $version = Parser::toVersion($this->getApplication()->getVersion());

        if ($update = $manifest->findRecent($version)) {
            $output->write(
                sprintf(
                    '<info>Updating conductor.phar from %s -> %s... </info>',
                    $version,
                    $update->getVersion()
                )
            );

            $manager = new Manager($manifest);
            if ($manager->update($version)) {
                $output->writeln("<info>Complete</info>");
            }
        } else {
            $output->writeln("<info>Currently running the latest conductor.phar: $version</info>");
        }
    }
}
