<?php

declare(strict_types=1);

namespace IMohamedSheta\Todo\Services;

/**
 * Service for resolving class names, namespaces, and functions from PHP files.
 */
class NamespaceResolverService
{
    /**
     * Extracts the fully qualified class name from a given PHP file using php tokenizer.
     *
     * @param  string  $filePath  The path to the PHP file.
     * @return string|null The fully qualified class name or null if the file does not exist.
     */
    public function getClassNameFromFile(string $filePath): ?string
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return null;
        }

        $tokens = token_get_all($content);
        $namespace = '';
        $className = '';
        $counter = count($tokens);

        for ($i = 0; $i < $counter; $i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                $namespace = $this->getFullNamespace($tokens, $i);
            }

            if ($tokens[$i][0] === T_CLASS && $tokens[$i - 1][0] !== T_DOUBLE_COLON) {
                $className = $tokens[$i + 2][1] ?? null;
                break;
            }
        }

        return $className !== null && $className !== '' && $className !== '0' ? trim($namespace.'\\'.$className, '\\') : null;
    }

    /**
     * Retrieves all function names defined in a given PHP file using PHP's tokenizer.
     *
     * @param  string  $filePath  The path to the PHP file.
     * @return array<int, string> List of function names found in the file.
     */
    public function getFunctionsFromFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        if ($content === false) {
            return [];
        }

        $tokens = token_get_all($content);
        $functions = [];
        $counter = count($tokens);

        for ($i = 0; $i < $counter; $i++) {
            if ($tokens[$i][0] === T_FUNCTION && isset($tokens[$i + 2][1])) {
                $functions[] = $tokens[$i + 2][1];
            }
        }

        return $functions;
    }

    /**
     * Extracts the full namespace from the tokenized PHP file content.
     *
     * @param  array<int, mixed>  $tokens  Tokenized PHP content.
     * @param  int  $index  The starting index of the namespace token.
     * @return string The extracted namespace.
     */
    private function getFullNamespace(array $tokens, int $index): string
    {
        $namespace = '';
        $counter = count($tokens);
        for ($i = $index + 2; $i < $counter; $i++) {
            if ($tokens[$i] === ';') {
                break;
            }
            if (is_array($tokens[$i])) {
                $namespace .= $tokens[$i][1];
            }
        }

        return trim($namespace);
    }
}
