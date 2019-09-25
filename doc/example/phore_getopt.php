#!/usr/bin/php
<?php

require __DIR__ . "/../../vendor/autoload.php";

function printHelp(string $errorMsg=null)
{
    if ($errorMsg !== null)
        $errorMsg .= "\nSee '" . $GLOBALS["argv"][0] . " --help'\n";
    echo <<<EOT
$errorMsg

Usage: {$GLOBALS["argv"][0]} [OPTIONS] [COMMAND]

Description of what this script is actually doing

Options:
  -h, --help              Show Help and exit
  -v                      Be more verbose
      --action string     Output version and quit
      
Commands:
  runner    Run the command

EOT;
}

// Load the options
$opts = phore_getopt(
    "hf:", // -h , -f string
    [
        "action:"  // --action string
    ]

);

if ($opts->has("h") || $opts->has("help")) {
    printHelp();
    exit;
}


echo "\nargument -f:        {$opts->get("f")}";
echo "\nargument --action   ". implode (", ", $opts->getArr("action", []));
echo "\n";

