<?php

use IMohamedSheta\Todo\Services\FileCollectorService;

it('can collect files from a directory', function (): void {
    $stubPath = __DIR__.'/../stubs';

    $fileCollectorService = new FileCollectorService;
    $files = $fileCollectorService->collectFiles($stubPath);

    expect($files)->toBeArray()->toHaveCount(3);
    expect($files[0])->toBeInstanceOf(SplFileInfo::class);
});

it('can collect specific files', function (): void {
    $filePath = __DIR__.'/../stubs/Example.php';

    $fileCollectorService = new FileCollectorService;
    $files = $fileCollectorService->collectFiles('', $filePath);

    expect($files)->toBeArray()->toHaveCount(1);
    expect($files[0]->getFilename())->toBe('Example.php');
});

it('returns empty array for non-existent directory', function (): void {
    $fileCollectorService = new FileCollectorService;
    $files = $fileCollectorService->collectFiles(__DIR__.'/../invalid_folder');

    expect($files)->toBeArray()->toBeEmpty();
});

it('skips non-existent files', function (): void {
    $invalidFile = __DIR__.'/../stubs/nonexistent.php';

    $fileCollectorService = new FileCollectorService;
    $files = $fileCollectorService->collectFiles('', $invalidFile);

    expect($files)->toBeArray()->toBeEmpty();
});
