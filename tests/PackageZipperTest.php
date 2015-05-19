<?php

namespace MyBuilder\Conductor;

class PackageZipperTest extends \PHPUnit_Framework_TestCase
{
    private $packageZipper;

    public function setUp()
    {
        $tempZipsBaseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'package-zipper';
        if (false == is_dir($tempZipsBaseDir)) {
            mkdir($tempZipsBaseDir, 0777);
        }

        $tempZipsDir = $tempZipsBaseDir . DIRECTORY_SEPARATOR . uniqid();
        mkdir($tempZipsDir, 0777);

        $this->packageZipper = new PackageZipper($tempZipsDir);
    }

    public function test_it_should_zip_the_package()
    {
        $file = __DIR__ . '/fixtures/packages/package-a/composer.json';

        $zipPath = $this->packageZipper->zip(new \SplFileInfo($file));

        $this->assertZipContains(array(
            'Package/composer.json' => file_get_contents($file),
            'Package/replace_with_symlink.path' => __DIR__ . '/fixtures/packages/package-a',
        ), $zipPath);
    }

    public function test_it_should_throw_an_mismatch_exception_when_zip_and_package_checksum_does_not_match()
    {
        $zipPath = $this->packageZipper->zip(new \SplFileInfo(
            __DIR__ . '/fixtures/packages/package-a/composer.json'));

        $this->setExpectedException('MyBuilder\Conductor\Exception\ChecksumMismatchException');

        $zipPath = $this->packageZipper->zip(new \SplFileInfo(
            __DIR__ . '/fixtures/packages/package-a-changed/composer.json'));
    }

    private function assertZipContains(array $contents, $file)
    {
        $zip = new \ZipArchive();
        $zip->open($file);

        $actual = array();
        for ($i = 0; $i < $zip->numFiles; $i++) {
            $actual[$zip->getNameIndex($i)] = $zip->getFromIndex($i);
        }

        $zip->close();

        $this->assertEquals($contents, $actual);
    }
}
