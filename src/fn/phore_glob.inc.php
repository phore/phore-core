<?php


// 
// phore_glob - Simple glob utility supporting * (single-level) and ** (multi-level).
// 
// This variant includes:
//  - An optional $rootPath parameter to restrict returned paths so they're always inside $rootPath.
//  - An optional $throwOnSecViolation (default=false) to throw exceptions if a match falls outside $rootPath.
//  - A $useCwd (default=true) flag: if false, relative paths are always resolved against $rootPath (instead of getcwd()).
// 
// Usage examples:
// 
//   // 1) Basic usage without root restriction:
//   $files = phore_glob("/var/www/**\/*.php");
// 
//   // 2) With $rootPath ensuring files can't escape that directory:
//   //    (pattern is absolute - must match /var/www, or security violation)
//   $files = phore_glob("/var/www/**/*.php", "/var/www", false);
// 
//   // 3) With root path and relative pattern => resolves under /var/www if $useCwd=false:
//   //    pattern "./**.php" => effectively /var/www/**.php
//   $files = phore_glob("./**.php", "/var/www", false, false);
// 
//   // 4) Throw an exception on violations:
//   try {
//       $files = phore_glob("/some/other/path/**/*.php", "/var/www", true);
//   } catch (RuntimeException $e) {
//       echo "Security violation: " . $e->getMessage();
//   }
//
function phore_glob(
    string $pattern,
    ?string $rootPath = null,
    bool $throwOnSecViolation = false,
    bool $useCwd = true
): array {
    // Normalize and realpath the root path if set
    $rootReal = null;
    if ($rootPath !== null) {
        $rootPath = rtrim($rootPath, '/');
        $rootReal = realpath($rootPath);
        if ($rootReal === false) {
            if ($throwOnSecViolation) {
                throw new RuntimeException("Root path '$rootPath' does not exist or is not accessible.");
            }
            return [];
        }
    }

    // Resolve pattern base if not using CWD
    if ($rootReal !== null && !$useCwd) {
        // If pattern is relative (no leading slash), prepend rootPath
        if (!str_starts_with($pattern, '/') && !preg_match('/^[A-Za-z]:\\\\/', $pattern)) {
            $pattern = rtrim($rootReal, '/') . '/' . ltrim($pattern, './');
        } else {
            // Pattern is absolute => check if inside root
            $patCheck = realpath(dirname($pattern));
            if ($patCheck === false || !phore_isPathWithinRoot($patCheck, $rootReal)) {
                if ($throwOnSecViolation) {
                    throw new RuntimeException("Pattern directory '$pattern' is outside root path '$rootReal'.");
                }
                return [];
            }
        }
    }

    // If '**' used => do recursive search
    if (str_contains($pattern, '**')) {
        // Separate baseDir from the segment after '**'
        $pos = strpos($pattern, '**');
        $baseDir = rtrim(substr($pattern, 0, $pos), '/');
        $baseDir = $baseDir === '' ? '.' : $baseDir;

        // Remainder is file pattern
        $rest = ltrim(substr($pattern, $pos + 2), '/');

        // Convert e.g. "*.php" -> ".*\.php" for a regex
        $regex = '/^' . str_replace('\*', '.*', preg_quote($rest, '/')) . '$/i';

        // Try to realpath the baseDir
        $baseDirReal = realpath($baseDir);
        if ($baseDirReal === false) {
            if ($throwOnSecViolation) {
                throw new RuntimeException("Base directory '$baseDir' not found.");
            }
            return [];
        }

        // If we have a root, ensure baseDir is within the root
        if ($rootReal !== null && !phore_isPathWithinRoot($baseDirReal, $rootReal)) {
            if ($throwOnSecViolation) {
                throw new RuntimeException("Base directory '$baseDir' is outside root path '$rootReal'.");
            }
            return [];
        }

        $results = [];
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($baseDirReal, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }
            if (preg_match($regex, $file->getFilename())) {
                $realFile = realpath($file->getPathname());
                if ($realFile === false) {
                    // Possibly a broken symlink
                    continue;
                }
                if ($rootReal !== null && !phore_isPathWithinRoot($realFile, $rootReal)) {
                    if ($throwOnSecViolation) {
                        throw new RuntimeException("File '$realFile' is outside root path '$rootReal'.");
                    }
                    continue;
                }
                $results[] = $realFile;
            }
        }
        return $results;
    }

    // No '**' => regular glob
    $globResults = glob($pattern) ?: [];
    $results = [];
    foreach ($globResults as $path) {
        $realFile = realpath($path);
        if ($realFile === false) {
            continue;
        }
        if ($rootReal !== null && !isPathWithinRoot($realFile, $rootReal)) {
            if ($throwOnSecViolation) {
                throw new RuntimeException("File '$realFile' is outside root path '$rootReal'.");
            }
            continue;
        }
        $results[] = $realFile;
    }
    return $results;
}

/**
 * Checks if $path is within $root (including being equal to $root).
 * Example:
 *   /var/www/somefile => inside /var/www
 *   /var/www => inside itself
 */
function phore_isPathWithinRoot(string $path, string $root): bool
{
    // Use string compare; realpath will unify symbolic links etc.
    if (strlen($path) < strlen($root)) {
        return false;
    }
    // Compare prefix
    if (substr($path, 0, strlen($root)) !== $root) {
        return false;
    }
    // If there's more to $path than $root, ensure next char is a directory separator
    if (strlen($path) > strlen($root)) {
        $nextChar = $path[strlen($root)];
        if ($nextChar !== DIRECTORY_SEPARATOR) {
            return false;
        }
    }
    return true;
}
