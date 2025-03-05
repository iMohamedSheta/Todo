<?php

declare(strict_types=1);

namespace IMohamedSheta\Todo\Services;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

class FileCollectorService
{
    /**
     * Collect files based on provided folders and/or specific files.
     *
     * @param  string  $absoluteFolders  Comma-separated list of folders
     * @param  string|null  $absoluteFiles  Comma-separated list of specific files
     * @return array<int, SplFileInfo>
     */
    public function collectFiles(string $absoluteFolders = '', ?string $absoluteFiles = null): array
    {
        $files = [];
        $excludedFiles = [];

        // Collect specific files if provided.
        if ($absoluteFiles !== null && $absoluteFiles !== '' && $absoluteFiles !== '0') {
            $filesList = array_map('trim', explode(',', $absoluteFiles));

            foreach ($filesList as $filePath) {
                $realPath = realpath($filePath);

                if ($realPath === false) {
                    continue;
                }

                $files[] = new SplFileInfo($realPath);
                $excludedFiles[$realPath] = true;
            }
        }

        // Collect files from the provided folders.
        if ($absoluteFolders !== '' && $absoluteFolders !== '0') {
            $folderPaths = array_map('trim', explode(',', $absoluteFolders));

            foreach ($folderPaths as $folder) {
                if (! is_dir($folder)) {
                    continue;
                }

                foreach ($this->scanFolderFiles($folder, $excludedFiles) as $file) {
                    $files[] = $file;
                }
            }
        }

        return $files;
    }

    /**
     * Scan a folder and return an array of SplFileInfo objects.
     *
     * @return array<int, SplFileInfo>
     */
    protected function scanFolderFiles(string $folder, array $excludedFiles = []): array
    {
        $files = [];

        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($folder, RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file instanceof SplFileInfo && $file->isFile() && ! isset($excludedFiles[$file->getRealPath()])) {
                $files[] = $file;
            }
        }

        return $files;
    }
}
