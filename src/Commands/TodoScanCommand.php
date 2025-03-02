<?php

declare(strict_types=1);

namespace IMohamedSheta\Todo\Commands;

use Illuminate\Console\Command;
use IMohamedSheta\Todo\Attributes\TODO;
use IMohamedSheta\Todo\Services\FileCollectorService;
use IMohamedSheta\Todo\Services\NamespaceResolverService;
use ReflectionClass;
use ReflectionFunction;
use SplFileInfo;

class TodoScanCommand extends Command
{
    protected $signature = 'todo {--src=app : The source directory that is going to be scanned for todos}';

    protected $description = 'Scan project for TODO attributes and display them.';

    public function __construct(
        protected FileCollectorService $fileCollectorService,
        protected NamespaceResolverService $namespaceResolverService
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info("\n🔍 Scanning for TODOs...\n");

        $files = $this->fileCollectorService->collectFiles($this->getScannedFolderFullPath());

        if ($files === []) {
            $this->info("✅ No TODOs found!\n");

            return 0;
        }

        $todos = $this->scanFiles($files);

        if ($todos === []) {
            $this->info("✅ No TODOs found!\n");

            return 0;
        }

        $tableHeader = ['Type', 'Class/Method', 'Message'];

        $this->table(
            $tableHeader,
            $todos,
        );

        $this->info("\n🎯 Total TODOs Found: ".count($todos)."\n");

        return 1;
    }

    /**
     * Scan files for TODOs
     *
     * @param  array<int, SplFileInfo>  $files
     * @return array<int, array{string, string, string}>
     */
    protected function scanFiles(array $files): array
    {
        $todos = [];
        foreach ($files as $file) {
            $className = $this->namespaceResolverService->getClassNameFromFile($file->getRealPath());

            if ($className && class_exists($className)) {
                $reflection = new ReflectionClass($className);
                $this->checkClassLevelAttributes($reflection, $todos);
                $this->checkMethodLevelAttributes($reflection, $todos);
            }

            $this->checkFunctionLevelAttributes($file, $todos);
        }

        return $todos;
    }

    /**
     *  Check function level attributes
     *
     * @param  array<int, array{string, string, string}>  &$todos
     */
    protected function checkFunctionLevelAttributes(SplFileInfo $file, array &$todos): void
    {
        $functions = $this->namespaceResolverService->getFunctionsFromFile($file->getRealPath());

        foreach ($functions as $function) {
            if (function_exists($function)) {
                $reflection = new ReflectionFunction($function);
                foreach ($reflection->getAttributes(TODO::class) as $attr) {
                    $todos[] = ['Function', $function, $attr->newInstance()->message];
                }
            }
        }
    }

    /**
     * @param  ReflectionClass<object>  $reflection
     * @param  array<int, array{string, string, string}>  $todos
     */
    protected function checkClassLevelAttributes(ReflectionClass $reflection, array &$todos): void
    {
        foreach ($reflection->getAttributes(TODO::class) as $attr) {
            $todos[] = ['Class', $reflection->getName(), $attr->newInstance()->message];
        }
    }

    /**
     * @param  ReflectionClass<object>  $reflection
     * @param  array<int, array{string, string, string}>  $todos
     */
    protected function checkMethodLevelAttributes(ReflectionClass $reflection, array &$todos): void
    {
        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes(TODO::class) as $attr) {
                $todos[] = ['Method', "{$reflection->getName()}::{$method->getName()}()", $attr->newInstance()->message];
            }
        }
    }

    /**
     * get scanned folder full path
     *
     * @return string scanned folder full path
     */
    private function getScannedFolderFullPath(): string
    {
        $src = is_string($this->option('src')) ? $this->option('src') : 'app';

        $basePath = dirname(__DIR__, 5);

        return $basePath.DIRECTORY_SEPARATOR.$src;
    }
}
