---
slugName: phore-glob-extended-doc
inlcudeFiles:
- src/fn/phore_glob.inc.php
editFiles:
- src/fn/phore_glob.inc.php
original_prompt: Ergänze die Beschreibung von phore_glob in src/fn/phore_glob.inc.php
  durch alle möglichen Examples erst ohne exclude aber mit einem und array patterns
  und dann auch mit exclude mit rekursion und allen möglichen edge cases. Und was
  ein Entwickler noch so wisssen muss in englisch.
---
# Instructions

This task requires updating the documentation for the `phore_glob` function in the file `src/fn/phore_glob.inc.php`. The aim is to extend the examples in the docblock so that it covers all possible use cases. The documentation should now include detailed examples written in English. Below you will find the plan for modifications and the additional information that a developer might need to understand usage scenarios, edge cases, and internal behavior.

## Files and Classes to Modify

- File: `src/fn/phore_glob.inc.php`
  - Class: `PhoreGlob`
  - Function: `phore_glob`

## Implementation Details

### src/fn/phore_glob.inc.php

#### Objective
Update the documentation comment (docblock) for the `phore_glob` function. The docblock should be enhanced by adding a comprehensive list of examples that explain the usage of the function across different scenarios. The examples must detail:
- Examples without an exclusion parameter, using both single pattern (string) and multiple patterns (array).
- Examples employing the exclusion parameter with recursive patterns, using:
  - A single exclusion pattern.
  - An array of exclusion patterns.
- Edge cases and additional developer notes regarding behavior, such as pattern matching subtleties and security considerations.

#### Changes

1. **Extend Docblock Examples:**
   - Add example usage for a simple file matching without any exclusions:
     - Example 1: `phore_glob("*.txt")`
       - Description: Returns all `.txt` files in the current directory. This is the basic usage with a simple pattern.
     - Example 2: `phore_glob(["*.js", "*.css"])`
       - Description: Returns all `.js` and `.css` files from the current working directory. Note that multiple patterns can be provided as an array.
  
   - Add examples demonstrating recursive search patterns:
     - Example 3: `phore_glob("src/**/*.php")`
       - Description: Recursively searches all subdirectories inside `src/` for `.php` files. This example demonstrates the use of double asterisks (`**`) for a multi-level directory search.
  
   - Add examples that include the use of the optional exclusion parameter:
     - Example 4: `phore_glob("src/**/*.php", "*.test.php")`
       - Description: Searches for `.php` files recursively under the `src/` directory, but excludes files that match the `*.test.php` pattern.
     - Example 5: `phore_glob("**/*.php", ["*Test.php", "*Fixture.php"])`
       - Description: Recursively searches for all `.php` files across directories while excluding files ending in `Test.php` or `Fixture.php`. This is useful for avoiding unit test files and fixture files.
  
   - Optionally, mention behavior when using the boolean mode for `$exclude`:  
     - Example 6: `phore_glob("*.md", true)`
       - Description: Searches for markdown files in the current directory and, when a matching file is found in an insecure location, the function throws an exception due to the `true` flag for `$exclude`. (Note: Check that your use case properly demands an exception for security purposes.)
  
2. **Developer Notes:**
   - Explain that the patterns support a single-level wildcard (`*`) and a multi-level wildcard (`**`) for directory recursion.
   - Caution that dots (.) in filenames are interpreted literally unless converted into regex-insensitive matches by the utility.
   - Note that the root directory is determined dynamically from the pattern provided and that relative vs absolute paths may behave differently.
   - Mention that the exclusion logic is applied after matching the primary pattern, and the exclusion patterns are evaluated using a similar conversion to regex as the match patterns.
   - Highlight that the internal implementation uses regular expression conversion and that edge cases (such as directory separators or escaping characters) should be considered when writing custom patterns.
   - Advise developers to review security implications when using the boolean mode for `$exclude`, as it may throw exceptions if paths are not within expected directories.

3. **Example Documentation Block Revision:**
   In the PHP docblock for the `phore_glob` function, provide the examples and detailed explanation as outlined above. The documentation must be in English and maintain a clear and concise style.

#### Example Docblock (Modified Section)
Below is a sample snippet of how the improved docblock might look:

------------------------------------------------------------
/**
 * phore_glob - Enhanced glob utility supporting * (single-level) and ** (multi-level) wildcards.
 *
 * This function allows searching for files based on glob-style patterns.
 *
 * Examples:
 *   // Without exclude parameter:
 *   phore_glob("*.txt");
 *     - Returns all .txt files in the current directory.
 *
 *   phore_glob(["*.js", "*.css"]);
 *     - Returns all .js and .css files from the current directory.
 *
 *   // Using recursive search with **
 *   phore_glob("src/**/*.php");
 *     - Searches recursively under 'src/' for .php files.
 *
 *   // With a single exclusion pattern:
 *   phore_glob("src/**/*.php", "*.test.php");
 *     - Searches recursively for .php files, excluding any files matching '*.test.php'.
 *
 *   // With multiple exclusion patterns:
 *   phore_glob("**/*.php", ["*Test.php", "*Fixture.php"]);
 *     - Recursively finds all .php files but excludes files that are test or fixture files.
 *
 *   // Enforcing security via exclude as boolean:
 *   phore_glob("*.md", true);
 *     - Retrieves markdown files, throwing exceptions for paths that violate security constraints.
 *
 * Developer Notes:
 *   - The function supports both string and array input for patterns.
 *   - Patterns use '*' for matching any characters within a single directory level and '**' for recursive matching.
 *   - Exclusion patterns are applied after a file matches the primary pattern.
 *   - The underlying implementation converts glob patterns to regular expressions; be cautious with special regex characters.
 *   - When using boolean values for the $exclude parameter, 'true' forces the function to throw exceptions on security violations,
 *     while 'false' will silently skip insecure paths.
 *
 * @param string|array $pattern The pattern or array of patterns to match files.
 * @param string|array|null $exclude Optional exclusion pattern(s) to omit matching files.
 * @return array Returns an array of file paths that match the given pattern(s) and are not excluded.
 */
------------------------------------------------------------

Follow these guidelines precisely to provide clear and helpful documentation for any developer using this utility.