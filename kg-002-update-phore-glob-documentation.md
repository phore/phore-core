---
slugName: update-phore-glob-documentation
inlcudeFiles:
- src/fn/phore_glob.inc.php
editFiles:
- src/fn/phore_glob.inc.php
original_prompt: Ergänze die Beschreibung von phore_glob in src/fn/phore_glob.inc.php
  durch alle möglichen Examples erst ohne exclude aber mit einem und array patterns
  und dann auch mit exclude mit rekursion und allen möglichen edge cases. Und was
  ein Entwickler noch so wisssen muss in englisch. Exclude is not a boolean. It is
  a array of patterns to exclude! Maybe rename this parameter.
---
# Instructions

The goal of this task is to expand and refine the documentation and examples in the file "src/fn/phore_glob.inc.php" related to the function `phore_glob` (and optionally its related class `PhoreGlob`). The focus is on fully illustrating the behavior, usage examples, and edge cases around both the pattern matching as well as the exclude functionality.

## Files and Classes to Modify

- **src/fn/phore_glob.inc.php**  
  - Modify both the file header documentation and the phpdoc block for the `phore_glob` function.  
  - Update class `PhoreGlob` as necessary in its inline documentation if needed.

## Implementation Details

### Objective

Enhance the documentation of the `phore_glob` function with comprehensive examples in English. The examples must cover:
- Usage with single or multiple patterns (supplied as an array) without exclusions.
- Usage with the new exclude functionality where the exclude parameter is always an array of exclude patterns (and not a boolean).
- Edge cases and recursive directory scanning, including ambiguous patterns and potential pitfalls.
- A note for developers regarding the nature of the exclude parameter and a suggestion for potentially renaming it (for example, changing `$exclude` to `$excludePatterns`).

### Changes

#### In "src/fn/phore_glob.inc.php"

1. **Update File Header Documentation:**
   - Extend the multiline comment at the top of the file. Include a detailed explanation of the exclude parameter:
     - Clarify that the `exclude` parameter is an array of patterns that if matched will cause files to be filtered out.
     - Emphasize that this parameter is not a boolean flag, and that developers should pass an array even when no exclusions are intended (or consider renaming it to something like `$excludePatterns`).
   - Mention that both `$pattern` and `$exclude` parameters can be provided as strings or arrays, and provide details on how they are internally processed.

2. **Enhance the phpdoc Block for phore_glob Function:**
   - Update the ppdoc block of the `phore_glob` function to include multiple examples:
     - **Example without Exclude (Using Array Patterns):**
       - Example demonstrating basic usage with patterns as an array. For instance:
         - "phore_glob(['*.txt', '*.md'])" should return all matching TXT and Markdown files from the current directory.
     - **Example with Exclude:**
       - Show an example that calls `phore_glob` with a pattern to include files recursively (using ** for recursion) and an exclusion list to ignore specific directories or file types.
         - For example: "phore_glob('src/**/*.php', ['src/vendor/*', 'src/tests/*'])" to include all PHP files in the `src` directory (including subdirectories) but exclude any files under `src/vendor` or `src/tests`.
     - **Edge Cases:**
       - If a pattern uses unexpected combinations such as mixed use of * and **, or if overlapping include and exclude rules are set, explain what behaviors are expected.
       - Clarify that the recursive behavior is controlled by the pattern provided (e.g., `**`).
   - Add developer notes:
     - Explain how file paths are normalized (backslashes are replaced by forward slashes).
     - Mention how uniqueness of results is enforced (using array_unique).
     - Suggest that if further clarity is required regarding the exclude parameter, renaming it to more reflective terminology should be considered.

3. **Prototypes and Documentation Examples:**

   Update the phpdoc block above the `phore_glob` function to include lines similar to:

   Example Documentation Addition:

   -----------------------------------------------------------
   /**
    * phore_glob - Enhanced glob utility supporting both single-level "*" and multi-level "**" wildcard patterns.
    *
    * Examples:
    * - Basic usage with a single pattern:
    *     phore_glob("*.txt")
    *       => Returns all .txt files in the current directory.
    *
    * - Usage with multiple patterns provided as an array:
    *     phore_glob(["*.txt", "*.md"])
    *       => Returns all .txt and .md files in the current directory.
    *
    * - Recursive search example without exclusions:
    *     phore_glob("src/**/*.php")
    *       => Returns all .php files in the "src" directory and its subdirectories.
    *
    * - Recursive search with exclude patterns:
    *     phore_glob("src/**/*.php", ["src/vendor/*", "src/tests/*"])
    *       => Returns all .php files under "src" except those inside "src/vendor" or "src/tests".
    *
    * Note:
    * - The $exclude parameter is expected to be an array of patterns to exclude, not a boolean value.
    *   Consider renaming it to $excludePatterns for clarity.
    * - Patterns in both include and exclude arrays support '*' for single-level matching and '**' for multi-level matching.
    * - All matching is case-insensitive and paths are normalized to use forward slashes ('/').
    *
    * @param string|array $pattern The pattern or an array of patterns to include in the search.
    * @param string|array|null $exclude An array of patterns to exclude from the search (must be an array). 
    *                                    (Consider renaming to $excludePatterns)
    * @return array List of file paths matching the criteria.
    */
   -----------------------------------------------------------

4. **Developer Recommendations:**
   - It is advisable to revise the parameter naming if future changes are considered. For example, renaming `$exclude` to `$excludePatterns` can eliminate confusion.
   - The new documentation should assist developers in understanding the intended usage, handling of wildcard patterns, and common edge scenarios such as overlapping include/exclude rules.
   - Revalidate that any changes remain backward compatible or document any breaking changes in a changelog if the parameter name is altered.

These changes should make the usage of `phore_glob` clearer, provide better examples and explanations for potential edge cases, and enhance overall maintainability and readability of the codebase documentation.