<?php

namespace Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    public function getPackageProviders($app): array
    {
        return [
            \IMohamedSheta\Todo\TodoServiceProvider::class,
        ];
    }
}
