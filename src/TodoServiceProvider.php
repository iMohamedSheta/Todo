<?php

namespace IMohamedSheta\Todo;

use Illuminate\Support\ServiceProvider;
use IMohamedSheta\Todo\Commands\TodoScanCommand;

class TodoServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                TodoScanCommand::class,
            ]);
        }
    }
}
