<?php

namespace MyBuilder\Conductor;

class PackageZipper
{
    private $zipsPath;

    public function __construct($zipsPath)
    {
        $this->zipsPath = $zipsPath;
    }

    /**
     * @param \SplFileInfo $composerFile the package composer.json file path
     *
     * @return string The package conductor file path
     */
    public function zip(\SplFileInfo $composerFile)
    {
        $json = json_decode(file_get_contents($composerFile), true);

        if (false === isset($json['version'])) {
            throw new \RuntimeException('Package "' . $json['name'] . '" has no version defined');
        }

        $packageZipPath = $this->getZipPath($json['name'], $json['version']);

        if (false === file_exists($packageZipPath)) {
            $this->createZip($composerFile, $packageZipPath);
        }

        $this->verifyZip($composerFile, $packageZipPath, $json);

        return $packageZipPath;
    }

    private function getZipPath($packageName, $version)
    {
        return $this->zipsPath . DIRECTORY_SEPARATOR . str_replace("/", '_', $packageName) . '_' . $version . '.zip';
    }

    private function createZip($composerFile, $zipPath)
    {
        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE);
        $zip->addFile($composerFile, 'Package/composer.json');
        $zip->addFromString('Package/replace_with_symlink.path', dirname($composerFile));
        $zip->close();
    }

    private function verifyZip($composerFile, $zipPath, $info)
    {
        $zip = new \ZipArchive();
        $zip->open($zipPath);
        $content = $zip->getFromName('Package/composer.json');
        $zip->close();

        if (sha1_file($composerFile) !== sha1($content)) {
            throw new Exception\ChecksumMismatchException('
            Package ' .  $info['name'] . '@' . $info['version'] . '
            is already zipped with the given version but
            the zip composer.json checksum does not match the package/composer.json checksum
            maybe you forgot to increment the package/composer.json version?');
        }
    }
}
