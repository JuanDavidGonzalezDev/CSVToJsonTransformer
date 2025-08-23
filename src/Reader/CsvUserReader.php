<?php

declare(strict_types=1);

namespace App\Reader;

class CsvUserReader
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function getRows(): \Generator
    {
        $handle = fopen($this->file, 'r');
        if (!$handle) {
            throw new \RuntimeException("Cannot open file: {$this->file}");
        }
        $header = fgetcsv($handle);
        while (($row = fgetcsv($handle)) !== false) {
            yield array_combine($header, $row);
        }
        fclose($handle);
    }
}
