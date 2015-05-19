<?php

namespace MyBuilder\Conductor\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class LockFixerCommand extends BaseCommand
{
    protected function configure()
    {
        parent::configure();

        $this
            ->setName("fix-composer-lock")
            ->setDescription('Fixes composer lock real-paths with relative-paths');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $fs = new Filesystem();
        $configuration = $this->getConfiguration();
        $conductorRealPaths = $this->getConductorRealPaths($configuration['artifacts_repository']);

        foreach ($this->createFinder()->in(getcwd()) as $lockFile) {
            $relativePaths = $this->getRelativePaths($fs, $lockFile, $conductorRealPaths);

            if ($this->fixLockFileRealPathsWithRelativePaths($lockFile, $conductorRealPaths, $relativePaths)) {
                $output->writeln('<info>Fixed lock file</info> "' . $lockFile . '"');
            }
        }
    }

    private function createFinder()
    {
        return Finder::create()
            ->files()
            ->exclude('vendor')
            ->name('composer.lock');
    }

    private function getConductorRealPaths($zipsDir)
    {
        $finder = Finder::create()->files()->name('*.zip')->in($zipsDir);

        return array_map(function (\SplFileInfo $file) {
            return $file->getRealPath();
        }, iterator_to_array($finder));
    }

    private function getRelativePaths(Filesystem $fs, \SplFileInfo $lockFile, array $conductorRealPaths)
    {
        return array_map(function ($conductorPath) use ($fs, $lockFile) {
            return $fs->makePathRelative(dirname($conductorPath), dirname($lockFile->getRealPath())) . basename($conductorPath);
        }, $conductorRealPaths);
    }

    private function fixLockFileRealPathsWithRelativePaths($lockFile, $conductorRealPaths, $relativePaths)
    {
        $initialContent = file_get_contents($lockFile);
        $fixedContent = str_replace($conductorRealPaths, $relativePaths, $initialContent);

        if ($initialContent === $fixedContent) {
            return false;
        }

        return file_put_contents($lockFile, $fixedContent);
    }
}
