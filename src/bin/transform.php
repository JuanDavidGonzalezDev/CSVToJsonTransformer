<?php

declare(strict_types=1);

echo "Start import.. \n";

require_once __DIR__ . '/../../vendor/autoload.php';

use App\Transformer\UserTransformer;
use App\Reader\CsvUserReader;
use App\Writer\JsonStreamWriter;

// CLI options
$options = getopt('', ['input:', 'output:']);
$inputFile = $options['input'] ?? 'input.csv';
$outputFile = $options['output'] ?? 'output.json';
$countriesFile = __DIR__ . '/../countries.json';

// Load country codes
// $countries = json_decode(file_get_contents($countriesFile), true);
try {
    $json = file_get_contents($countriesFile);
    if ($json === false) {
        throw new RuntimeException("Could not read country file: {$countriesFile}");
    }
    $countries = json_decode($json, true);
} catch (Throwable $e) {
    echo "Error loading countries: " . $e->getMessage() . "\n";
    exit(1);
}

$reader = new CsvUserReader($inputFile);
$transformer = new UserTransformer($countries);
$writer = new JsonStreamWriter($outputFile);

// Stream transform
try {
    $writer->open();
    foreach ($reader->getRows() as $row) {
        $user = $transformer->transform($row);
        if ($user !== null) {
            $writer->write($user);
        }
    }
    $writer->close();

    echo "Done. Output written to $outputFile\n";
} catch (\Throwable $e) {
    echo "Error during transformation or writing:: " . $e->getMessage() . "\n";
    exit(1);
}
