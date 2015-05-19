<?php

namespace MyBuilder\Conductor;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\Finder;

class Conductor
{
    /**
     * @var Filesystem
     */
    private $fs;

    public function __construct(Filesystem $fs)
    {
        $this->fs = $fs;
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

    private function symlinkPackageToVendor($packagePath, $vendorPath)
    {
        $relative = $this->fs->makePathRelative(realpath($packagePath), realpath($vendorPath . '/../'));

        $this->fs->rename($vendorPath, $vendorPath . '_linked', true);
        $this->fs->symlink($relative, $vendorPath);
        $this->fs->remove($vendorPath . '_linked');
    }
}
