<?php

namespace IMohamedSheta\Todo\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use IMohamedSheta\Todo\Attributes\TODO;
use IMohamedSheta\Todo\Services\FileCollectorService;
use ReflectionClass;
use ReflectionFunction;
use SplFileInfo;

class TodoScanCommand extends Command
{
    protected $signature = 'todo {--src=app : The source directory that is going to be scanned for todos}';

    protected $description = 'Scan project for TODO attributes and display them.';

    public function __construct(protected FileCollectorService $fileCollectorService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info("\n🔍 Scanning for TODOs...\n");

        $src = is_string($this->option('src')) ? $this->option('src') : 'app';

        // Collect PHP files
        $files = $this->fileCollectorService->collectFiles($src);

        if ($files->isEmpty()) {
            $this->info("✅ No TODOs found!\n");

            return 0;
        }

        // Scan for TODOs
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

        $this->info("\n🎯 Total TODOs Found: " . count($todos) . "\n");

        return 1;
    }

    /**
     * Scan files for TODOs
     *
     * @param  Collection<int, SplFileInfo>  $files
     * @return array<int, array{string, string, string}>
     */
    protected function scanFiles(Collection $files): array
    {
        $todos = [];

        foreach ($files as $file) {
            $className = $this->getClassNameFromFile($file->getRealPath());

            if ($className && class_exists($className)) {

                $reflection = new ReflectionClass($className);

                // Check class-level #[TODO] attribute
                $this->checkClassLevelAttributes($reflection, $todos);

                // Check method-level #[TODO] attributes
                $this->checkMethodLevelAttributes($reflection, $todos);
            }

            // Check function-level  #[TODO] attributes
            $this->checkFunctionLevelAttributes($file, $todos);
        }

        return $todos;
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
     * @param  array<int, array{string, string, string}>  $todos
     */
    protected function checkFunctionLevelAttributes(SplFileInfo $file, array &$todos): void
    {
        $realPath = $file->getRealPath();

        $functions = $this->getFunctionsFromFile($realPath);

        foreach ($functions as $function) {
            if (function_exists($function)) {
                $reflection = new ReflectionFunction((string) $function);
                foreach ($reflection->getAttributes(TODO::class) as $attr) {
                    $filePathStartFromBasePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $realPath); // @phpstan-ignore-line
                    $todos[] = ['Function', $filePathStartFromBasePath . ' -> ' . $function . '()', $attr->newInstance()->message];
                }
            }
        }
    }

    /**
     * Get all functions from a file
     *
     * @return array<int, string>
     */
    protected function getFunctionsFromFile(string $filePath): array
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            return [];
        }
        // Extract all functions
        preg_match_all('/function\s+(\w+)\s*\(/', $content, $matches);

        return $matches[1];
    }

    /**
     *  Get class name from file
     */
    protected function getClassNameFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);

        if ($content === false) {
            return null;
        }

        // Extract namespace
        preg_match('/namespace\s+([\w\\\\]+);/', $content, $namespaceMatch);
        $namespace = $namespaceMatch[1] ?? '';

        // Extract class name
        if (preg_match('/class\s+(\w+)/', $content, $classMatch)) {
            return $namespace !== '' && $namespace !== '0' ? "{$namespace}\\{$classMatch[1]}" : $classMatch[1];
        }

        return null;
    }
}
