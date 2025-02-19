<?php

declare(strict_types=1);

namespace IMohamedSheta\Todo\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

class FileCollectorService
{
    /**
     * Collect files based on provided folders and/or specific files.
     *
     * @param  string  $foldersInput  comma-separated list of folders
     * @param  string|null  $filesInput  comma-separated list of specific files
     * @return Collection<int , \SplFileInfo>
     */
    public function collectFiles(string $foldersInput = '', ?string $filesInput = null): Collection
    {
        /** @var Collection<int, \SplFileInfo> $files */
        $files = collect([]);

        // Collect specific files if provided.
        if ($filesInput !== null && $filesInput !== '' && $filesInput !== '0') {
            $filesList = array_map('trim', explode(',', $filesInput));

            foreach ($filesList as $filePath) {
                $absolutePath = base_path($filePath); // @phpstan-ignore-line

                if (! File::exists($absolutePath)) {
                    // TODO Log or warn here if a file doesn't exist.
                    continue;
                }
                // For consistency, pushing files to an SplFileInfo object.
                $files->push(new \SplFileInfo($absolutePath));
            }
        }

        // Collect files from the provided folders.
        if ($foldersInput !== '' && $foldersInput !== '0') {
            $folderPaths = array_map('trim', explode(',', $foldersInput));

            foreach ($folderPaths as $folder) {
                $absoluteFolder = base_path($folder); // @phpstan-ignore-line

                if (! File::exists($absoluteFolder)) {
                    // TODO Log or warn here if a folder doesn't exist.
                    continue;
                }

                // File::allFiles returns an array of SplFileInfo objects.
                $folderFiles = File::allFiles($absoluteFolder);
                $files = $files->merge($folderFiles);
            }
        }

        return $files;
    }
}
