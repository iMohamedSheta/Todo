<?php

namespace IMohamedSheta\Todo;

use Illuminate\Support\ServiceProvider;
use IMohamedSheta\Todo\Commands\TodoScanCommand;
use IMohamedSheta\Todo\Services\FileCollectorService;

class TodoServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FileCollectorService::class, fn($app): \IMohamedSheta\Todo\Services\FileCollectorService => new FileCollectorService);
    }

    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TodoScanCommand::class,
            ]);
        }
    }
}
