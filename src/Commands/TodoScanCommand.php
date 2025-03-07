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

        $src = is_string($this->option('src')) ? $this->option('src') : 'app';

        $files = $this->fileCollectorService->collectFiles($this->getScannedFolderFullPath($src));

        if ($files === []) {
            $this->info("✅ No TODOs found!\n");

            return 0;
        }

        $todos = $this->scanFiles($files);

        if ($todos === []) {
            $this->info("✅ No TODOs found!\n");

            return 0;
        }

        $tableHeader = ['Type', 'Class/Method/Function', 'Priority', 'Message'];

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
     * @return array<int, array{string, string, string, string}> $todos
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
     * @param  array<int, array{string, string, string, string}>  &$todos
     */
    protected function checkFunctionLevelAttributes(SplFileInfo $file, array &$todos): void
    {
        $realPath = $file->getRealPath();
        $functions = $this->namespaceResolverService->getFunctionsFromFile($realPath);

        foreach ($functions as $function) {
            if (function_exists($function)) {
                $reflection = new ReflectionFunction($function);
                foreach ($reflection->getAttributes(TODO::class) as $attribute) {
                    $attribute = $attribute->newInstance();
                    $todos[] = ['Function',  $this->getFilePathStartFromBasePath($realPath).' -> '.$function.'()', $attribute->priority->label(), $attribute->message];
                }
            }
        }
    }

    /**
     * @param  ReflectionClass<object>  $reflection
     * @param  array<int, array{string, string, string, string}>  $todos
     */
    protected function checkClassLevelAttributes(ReflectionClass $reflection, array &$todos): void
    {
        foreach ($reflection->getAttributes(TODO::class) as $attribute) {
            $attribute = $attribute->newInstance();
            $todos[] = ['Class', $reflection->getName(), $attribute->priority->label(), $attribute->message];
        }
    }

    /**
     * @param  ReflectionClass<object>  $reflection
     * @param  array<int, array{string, string, string, string}>  $todos
     */
    protected function checkMethodLevelAttributes(ReflectionClass $reflection, array &$todos): void
    {
        foreach ($reflection->getMethods() as $method) {
            foreach ($method->getAttributes(TODO::class) as $attribute) {
                $attribute = $attribute->newInstance();
                $todos[] = ['Method', "{$reflection->getName()}::{$method->getName()}()", $attribute->priority->label(), $attribute->message];
            }
        }
    }

    /**
     * @param  string  $realPath  full path of the file
     * @return string file path start from the base path of the project
     */
    private function getFilePathStartFromBasePath(string $realPath): string
    {
        return str_replace($this->getBasePath().DIRECTORY_SEPARATOR, '', $realPath);
    }

    /**
     * get scanned folder full path
     *
     * @param  string  $src  scanned folder based on the base project path
     * @return string scanned folder full path
     */
    private function getScannedFolderFullPath(string $src): string
    {
        return $this->getBasePath().DIRECTORY_SEPARATOR.$src;
    }

    /**
     *  get base path of the project
     *
     * @return string base path of the project
     */
    private function getBasePath(): string
    {
        return dirname(__DIR__, 5);
    }
}
