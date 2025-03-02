<?php

declare(strict_types=1);

namespace IMohamedSheta\Todo;

use Illuminate\Support\ServiceProvider;
use IMohamedSheta\Todo\Services\FileCollectorService;
use IMohamedSheta\Todo\Services\NamespaceResolverService;

class TodoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                \IMohamedSheta\Todo\Commands\TodoScanCommand::class,
            ]);
        }
    }

    public function register(): void
    {
        $this->app->singleton(FileCollectorService::class, fn ($app): \IMohamedSheta\Todo\Services\FileCollectorService => new FileCollectorService);
        $this->app->singleton(NamespaceResolverService::class, fn ($app): \IMohamedSheta\Todo\Services\NamespaceResolverService => new NamespaceResolverService);
    }
}
