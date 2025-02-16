<?php

/**
 * phore_glob - Enhanced glob utility supporting * (single-level) and ** (multi-level).
 *
 * This version includes:
 *  - Searching for files using wildcard patterns.
 *  - An optional $exclude parameter, which is:
 *    - An array of patterns specifying files or folders to exclude.
 *  - Results are normalized using forward slashes ('/') instead of backslashes.
 *  - The recursive search is controlled by the pattern provided (`**` allows traversal into subdirectories).
 *
 * Notes:
 * - The `$exclude` parameter should always be an array of patterns to exclude.
 * - In future versions, consider renaming `$exclude` to `$excludePatterns` to avoid confusion.
 */
class PhoreGlob
{
    /**
     * Search files matching the given patterns while considering exclusion patterns.
     *
     * @param string|array $pattern The search pattern(s).
     * @param string|array|null $excludePatterns An array of patterns specifying files/directories to exclude.
     *                                           (Previously `$exclude`, renamed for clarity).
     * @return array The list of matched file paths.
     */
    public static function search(string|array $pattern, string|array|null $excludePatterns = null): array
    {
        $patternArr = (array)$pattern;
        $excludeArr = (array)$excludePatterns;
        $results = [];

        foreach ($patternArr as $pat) {
            $rootDir = explode('*', $pat, 2)[0];
            $rootDir = dirname($rootDir);
            $searchPattern = $pat;

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($rootDir ?: '.',
                    FilesystemIterator::SKIP_DOTS | FilesystemIterator::FOLLOW_SYMLINKS)
            );

            foreach ($iterator as $file) {
                if (!$file->isFile()) {
                    continue;
                }
                $filePath = $rootDir
                    ? $rootDir . '/' . $iterator->getSubPathName()
                    : $iterator->getSubPathName();
                $filePath = str_replace('\\', '/', $filePath);

                if (self::matchPattern($filePath, $searchPattern) && !self::isExcluded($filePath, $excludeArr)) {
                    $results[] = $filePath;
                }
            }
        }

        return array_values(array_unique($results));
    }

    private static function matchPattern(string $filePath, string $pattern): bool
    {
        $regex = str_replace(['.', '**'], ['\\.', '.+'], $pattern);
        $regex = str_replace('*', '[^/]*', $regex);
        return (bool)preg_match('#^' . $regex . '$#i', $filePath);
    }

    private static function isExcluded(string $filePath, array $excludes): bool
    {
        foreach ($excludes as $ex) {
            $regex = str_replace(['.', '**'], ['\\.', '.+'], $ex);
            $regex = str_replace('*', '[^/]*', $regex);
            if (preg_match('#^' . $regex . '$#i', $filePath)) {
                return true;
            }
        }
        return false;
    }
}

/**
 * phore_glob - Enhanced glob utility supporting * (single-level) and ** (multi-level).
 *
 * Examples:
 * - Basic usage with a single pattern:
 *     phore_glob("*.txt")
 *       => Returns all .txt files in the current directory.
 *
 * - Multiple patterns provided as an array:
 *     phore_glob(["*.txt", "*.md"])
 *       => Returns all .txt and .md files in the current directory.
 *
 * - Recursive search example:
 *     phore_glob("src/**\/*.(php|ts)")
 *       => Returns all .php files in "src" and its subdirectories.
 *
 * - Recursive search with exclusion:
 *     phore_glob("src/**\/*.php", ["src/vendor/*", "src/tests/*"])
 *       => Returns all .php files under "src" except those in "src/vendor" or "src/tests".
 *
 * Notes:
 * - The `$exclude` parameter is expected to be an array of patterns to exclude, **not a boolean**.
 *   Consider renaming it to `$excludePatterns` for clarity.
 * - Patterns support `*` for single-level matching and `**` for multi-level matching.
 * - Path matching is case-insensitive, and paths are normalized to forward slashes (`/`).
 *
 * @param string|array $pattern The patterns to match.
 * @param string|array|null $exclude An array of patterns specifying items to exclude.
 * @return array List of file paths satisfying the search criteria.
 */
function phore_glob(string|array $pattern, string|array|null $exclude = null): array
{
    return PhoreGlob::search($pattern, $exclude);
}

/**
 * Checks if a given path is within the specified root path, including being equal to root.
 *
 * @param string $path The path to check.
 * @param string $root The root directory to compare against.
 * @return bool True if the path is within the root, false otherwise.
 */
function phore_isPathWithinRoot(string $path, string $root): bool
{
    $path = realpath($path);
    $root = realpath($root);

    if ($path === false || $root === false) {
        return false;
    }

    if (strpos($path, $root) !== 0) {
        return false;
    }

    // Ensure any extra characters after root form a valid subpath
    return strlen($path) === strlen($root) || $path[strlen($root)] === DIRECTORY_SEPARATOR;
}