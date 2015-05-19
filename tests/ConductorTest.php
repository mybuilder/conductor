<?php

namespace MyBuilder\Conductor;

use Symfony\Component\Filesystem\Filesystem;

class ConductorTest extends \PHPUnit_Framework_TestCase
{
    private $conductor;
    private $fs;

    public function setUp()
    {
        $this->fs = new Filesystem();
        $this->conductor = new Conductor($this->fs);
    }

    public function test_it_should_update_all_packages()
    {
        $zipperMock = $this->getMock('MyBuilder\Conductor\PackageZipper', array(), array(array()));
        $zipperMock
            ->expects($this->any())
            ->method('zip')
            ->will($this->returnCallback(function($a) {
                return $a;
            }));

        $files = $this->conductor->updatePackages(
            array(__DIR__ . '/fixtures/packages/*'),
            $zipperMock);

        $this->assertEquals(
            array(
                __DIR__ . '/fixtures/packages/package-a/composer.json',
                __DIR__ . '/fixtures/packages/package-a-changed/composer.json',
            ),
            array_map(null, $files));
    }

    public function test_it_should_symlink_packages()
    {
        $tempDir = $this->createTempDir();
        $this->fs->mirror(__DIR__ . '/fixtures/symlink', $tempDir);
        file_put_contents($tempDir . '/package-b/package-a/replace_with_symlink.path', $tempDir . '/package-a/');

        $this->conductor->symlinkPackages($tempDir);

        $link = $tempDir . '/package-b/package-a';
        $this->assertTrue(is_link($link), $link . ' should be a symlink');
        $this->assertEquals('../package-a/', readlink($link), 'It should have a relative symlink');
    }

    public function test_it_should_fix_composer_lock_absolute_paths()
    {
        $this->markTestIncomplete();

        // real paths to the conductor zips
        // will be made relative from the point of view of the composer.lock file
    }

    private function createTempDir()
    {
        $tempDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid();
        mkdir($tempDir, 0777);

        return $tempDir;
    }
}
