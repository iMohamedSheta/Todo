<?php

namespace Tests\Feature;

use IMohamedSheta\Todo\Services\FileCollectorService;
use Mockery;

it('returns 0 founds TODOs', function (): void {
    $this->artisan('todo')
        ->expectsOutput("\n🔍 Scanning for TODOs...\n")
        ->expectsOutput("✅ No TODOs found!\n")
        ->assertExitCode(0);
});

it('returns found TODOs', function (): void {

    $mockService = Mockery::mock(FileCollectorService::class);

    // Manually create an array of SplFileInfo objects pointing to stub files
    $mockFiles = [
        new \SplFileInfo(dirname(__DIR__, 1).'/stubs/Example.php'),
        new \SplFileInfo(dirname(__DIR__, 1).'/stubs/Example/InsiderExample.php'),
        new \SplFileInfo(dirname(__DIR__, 1).'/stubs/FunctionExample.php'),
    ];

    // Mock collectFiles to return these stub files
    $mockService->shouldReceive('collectFiles')->andReturn($mockFiles);

    app()->bind(FileCollectorService::class, fn () => $mockService);

    $this->artisan('todo --src=stubs')
        ->expectsOutput("\n🔍 Scanning for TODOs...\n")
        ->expectsOutputToContain('🎯 Total TODOs Found: 5')
        ->assertExitCode(1);
});
