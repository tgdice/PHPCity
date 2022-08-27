<?php

require 'php-ast/util.php';
require 'functions.php';

error_reporting(E_ERROR | E_WARNING | E_PARSE);

if ($argv[1]) {
    $baseDir = trim($argv[1]);
} else {
    print "Please input path to folder with files that will be processed: ";
    $fp       = fopen('php://stdin', 'r');
    $baseDir  = trim(fgets($fp, 1024));
}

$project  = preg_replace('/\//', '-', ltrim($baseDir,'/'));

try {
    $directory = new RecursiveDirectoryIterator($baseDir);
} catch (UnexpectedValueException $e) {
    print "The folder could not be found!\n";
    die;
}

$iterator  = new RecursiveIteratorIterator($directory);
$regex     = new RegexIterator($iterator, '/^.+\.php$/i', RecursiveRegexIterator::GET_MATCH);

$finalData = [];

foreach ($regex as $name => $object) {
    $data = [
        // "file" => str_replace('/','\\',ltrim(str_replace($baseDir, '', $name),'/'))
        "file" => ltrim(str_replace($baseDir, '', $name),'/')
    ];

    // Ignore vendor folder if present
    if (strpos($name, 'vendor/') !== false) {
        continue;
    }

    $ast = ast\parse_file($name, $version = 90);

    // list of nodes in file
    if ($ast instanceof ast\Node) {
        foreach ($ast->children as $i => $child) {
            $data = parseNode($child, $data);
        }
    }

    if (! isset($data['type'])) {
        continue;
    }

    array_push($finalData, $data);
}

$fileName = generateJSONFile($finalData, $project);

print '** JSON file generated: ./output/' . $fileName . ".json\n";
