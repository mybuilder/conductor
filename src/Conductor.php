<?php

namespace MyBuilder\Conductor;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Conductor
{
    /**
     * @var Filesystem
     */
    private $fileSystem;

    public function __construct(Filesystem $fileSystem)
    {
        $this->fileSystem = $fileSystem;
    }

    public function updatePackages($paths, PackageZipper $packageZipper)
    {
        $finder = new Finder();
        $finder->files()->exclude('vendor')->name('composer.json')->depth(0);

        $results = array();
        foreach ($finder->in($paths) as $file) {
            $results[] = $packageZipper->zip($file);
        }

        return $results;
    }

    public function symlinkPackages($rootPath)
    {
        $finder = new Finder();
        $finder->files()->name('replace_with_symlink.path');

        foreach ($finder->in($rootPath) as $file) {
            $this->symlinkPackageToVendor(file_get_contents($file), dirname($file));
        }
    }

    public function copyPackages($rootPath)
    {
        $finder = new Finder();
        $finder->files()->name('replace_with_symlink.path');

        foreach ($finder->in($rootPath) as $file) {
            $this->copyPackageToVendor(file_get_contents($file), dirname($file));
        }
    }

    private function symlinkPackageToVendor($packagePath, $vendorPath)
    {
        $relative = $this->fileSystem->makePathRelative(realpath($packagePath), realpath($vendorPath . '/../'));

        $this->fileSystem->rename($vendorPath, $vendorPath . '_linked', true);
        $this->fileSystem->symlink($relative, $vendorPath);
        $this->fileSystem->remove($vendorPath . '_linked');
    }

    private function copyPackageToVendor($packagePath, $vendorPath)
    {
        $directoryIterator = new \RecursiveDirectoryIterator($packagePath, \RecursiveDirectoryIterator::SKIP_DOTS);
        $iterator = new \RecursiveIteratorIterator($directoryIterator, \RecursiveIteratorIterator::SELF_FIRST);

        foreach ($iterator as $item) {
            if ($item->isDir()) {
                $this->fileSystem->mkdir($vendorPath . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            } else {
                $this->fileSystem->copy($item, $vendorPath . DIRECTORY_SEPARATOR . $iterator->getSubPathName());
            }
        }
    }
}
