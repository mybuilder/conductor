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
        $fileSystem = new Filesystem();
        $configuration = $this->getConfiguration();
        $conductorRealPaths = $this->getConductorRealPaths($configuration['artifacts_repository']);

        foreach ($this->createFinder()->in(getcwd()) as $lockFile) {
            $relativePaths = $this->getRelativePaths($fileSystem, $lockFile, $conductorRealPaths);

            if ($this->fixLockFileRealPathsWithRelativePaths($fileSystem, $lockFile, $conductorRealPaths, $relativePaths)) {
                $output->writeln('<info>Fixed lock file</info> "' . $lockFile . '"');
            }
        }
    }

    /**
     * @return Finder
     */
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

    private function getRelativePaths(Filesystem $fileSystem, \SplFileInfo $lockFile, array $conductorRealPaths)
    {
        return array_map(function ($conductorPath) use ($fileSystem, $lockFile) {
            return $fileSystem->makePathRelative(dirname($conductorPath), dirname($lockFile->getRealPath())) . basename($conductorPath);
        }, $conductorRealPaths);
    }

    private function fixLockFileRealPathsWithRelativePaths(Filesystem $fileSystem, $lockFile, $conductorRealPaths, $relativePaths)
    {
        $initialContent = file_get_contents($lockFile);
        $fixedContent = str_replace($conductorRealPaths, $relativePaths, $initialContent);

        if ($initialContent === $fixedContent) {
            return false;
        }
        $fileSystem->dumpFile($lockFile, $fixedContent);

        return true;
    }
}
