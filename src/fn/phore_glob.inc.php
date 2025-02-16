<?php

/**
 * phore_glob - Enhanced glob utility supporting * (single-level) and ** (multi-level).
 * 
 * This version includes:
 *  - An optional $rootPath parameter to restrict returned paths.
 *  - An optional $exclude parameter, which can:
 *    - Be an array of patterns to exclude.
 *    - Be a boolean (true to throw exceptions for security violations, false to silently skip).
 *  - An $absolute parameter to return absolute paths.
 */

function phore_glob(
    string $pattern,
    ?string $rootPath = null,
    array $exclude = null, // Can be an array of patterns or a boolean flag
    bool $absolute = false
): array {

    $results = [];

    // If no '**' in pattern, use glob() for simple matching
    if (strpos($pattern, '**') === false) {
        $files = glob($pattern);
        foreach ($files as $file) {
            // Convert path if absolute is requested
            $resolved = $absolute ? realpath($file) : $file;

            // Validate against rootPath if provided
            if ($rootPath !== null) {
                $absFile = realpath($resolved);
                if (!phore_isPathWithinRoot($absFile, realpath($rootPath))) {
                    if ($exclude === true) {
                        throw new RuntimeException("Security violation: $absFile is outside of rootPath $rootPath");
                    }
                    continue;
                }
            }

            // Apply exclusion patterns if $exclude is an array
            if (is_array($exclude)) {
                if ($rootPath !== null) {
                    $relativePath = ltrim(str_replace(realpath($rootPath), '', realpath($resolved)), '\/\\\\');
                } else {
                    $relativePath = basename($resolved);
                }
                foreach ($exclude as $exPattern) {
                    if (fnmatch($exPattern, $relativePath)) {
                        // Skip this file if it matches one of the exclude patterns.
                        continue 2;
                    }
                }
            }

            $results[] = $resolved;
        }
        return $results;
    }

    // Handle '**' expansion
    [$baseDir, $subPattern] = explode('**', $pattern, 2);
    $baseDir = rtrim($baseDir, '\/\\\\') ?: '.';
    $subPattern = ltrim(str_replace('\\\\', '/', $subPattern), '/');

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($baseDir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        $filePath = $file->getPathname();
        $relativePath = ltrim(str_replace(realpath($baseDir), '', realpath($filePath)), '\/\\\\');

        // Apply exclusion patterns if $exclude is an array
        if (is_array($exclude)) {
            $skip = false;
            foreach ($exclude as $exPattern) {
                if (fnmatch($exPattern, $relativePath)) {
                    $skip = true;
                    break;
                }
            }
            if ($skip) {
                continue;
            }
        }

        // Match file based on pattern
        $matchTarget = (strpos($subPattern, '/') === false) ? $file->getBasename() : $relativePath;
        if (!fnmatch($subPattern, $matchTarget)) {
            continue;
        }

        // Check rootPath restrictions
        if ($rootPath !== null) {
            $absRoot = realpath($rootPath);
            $absFile = realpath($filePath);
            if (!phore_isPathWithinRoot($absFile, $absRoot)) {
                if ($exclude === true) {
                    throw new RuntimeException("Security violation: $absFile is outside of rootPath $rootPath");
                }
                continue;
            }
        }

        // Convert to absolute path if requested
        $results[] = $absolute ? realpath($filePath) : $filePath;
    }

    return $results;
}

/**
 * Checks if a given path is within the specified root path, including being equal to root.
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