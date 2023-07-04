<?php

declare(strict_types=1);

namespace App\Service;

use Exception;
use ZipArchive;

class QtiItemPackageCreator implements QtiItemPackageCreatorInterface
{
    private string $resourcePath;

    public function __construct(string $resourcePath)
    {
        $this->resourcePath = $resourcePath;
    }

    /**
     * @throws Exception
     */
    public function createQtiItemPackage(string $id, array $quiz, string $label): string
    {
        // Define an array of key-value pairs to replace in the file
        $replacements = array(
            '{{id}}' => $id,
            '{{label}}' => $label,
            '{{question}}' => $quiz['Question'],
            '{{correct-identifier}}' => $quiz['Correct Identifier'],
            '{{question-A-text}}' => $quiz['A'],
            '{{question-B-text}}' => $quiz['B'],
            '{{question-C-text}}' => $quiz['C'],
            '{{question-D-text}}' => $quiz['D'],
        );

        $tmpPath = sprintf('%s/tmp/%s/', $this->resourcePath, $id);

        $filePaths = [
            'imsmanifest.xml' => 'imsmanifest.xml',
            'qti.xml' => sprintf('%s/qti.xml', $id),
        ];

        $this->createItemFiles($id, $replacements, $filePaths, $tmpPath);

        $zipName = $this->createZipPackage($id, $filePaths, $tmpPath);

        foreach ($filePaths as $src => $dest) {
            unlink($tmpPath . $src);
        }
        rmdir($tmpPath);

        return $zipName;
    }

    private function createItemFiles(string $id, array $replacements, array $filePaths, string $tmpPath): void
    {
        $templatePath = sprintf('%s/package-template/', $this->resourcePath);

        // Specify the path to the file to update
        foreach ($filePaths as $src => $dest) {
            $fileContent = file_get_contents($templatePath . $src);
            foreach ($replacements as $placeholder => $value) {
                $fileContent = str_replace($placeholder, $value, $fileContent);
            }
            @mkdir(dirname($tmpPath . $src), 0700, true);
            file_put_contents($tmpPath . $src, $fileContent);
        }
    }

    /**
     * @throws Exception
     */
    private function createZipPackage(string $id, array $filePaths, string $tmpPath): string
    {
        $zipPath = sprintf('%s/zip/', $this->resourcePath);
        $zipName = sprintf('%sgeneratedPackage-%s.zip', $zipPath, $id);

        $zip = new ZipArchive();
        if ($zip->open($zipName, ZipArchive::CREATE) !== true) {
            throw new Exception("Failed to create zip file");
        }

        foreach ($filePaths as $src => $dest) {
            $zip->addFile($tmpPath . $src, $dest);
        }

        $zip->close();

        return $zipName;
    }
}
