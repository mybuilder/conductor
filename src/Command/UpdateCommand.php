<?php

namespace MyBuilder\Conductor\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;
use MyBuilder\Conductor\PackageZipper;

class UpdateCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName("update")
            ->setDescription('Updates artifacts repository');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $configuration = $this->getConfiguration();

        if (false == isset($configuration['artifacts_repository'])) {
            throw new \RuntimeException('Missing "conductor.artifacts_repository" configuration');
        }

        if (false == isset($configuration['packages'])) {
            throw new \RuntimeException('Missing "conductor.packages" configuration');
        }

        $output->writeln('<info>Zipping packages</info>');

        $this->ensureBuildDir($configuration['artifacts_repository']);

        $this->conductor->updatePackages($configuration['packages'], new PackageZipper($configuration['artifacts_repository']));
    }

    private function ensureBuildDir($path)
    {
        $fs = new Filesystem();
        if (false == $fs->exists($path)) {
            $fs->mkdir($path);
        }
    }
}
