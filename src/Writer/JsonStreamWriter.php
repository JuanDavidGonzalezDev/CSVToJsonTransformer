<?php

declare(strict_types=1);

namespace App\Writer;

class JsonStreamWriter
{
    private string $file;
    private $handle;
    private bool $first = true;

    public function __construct(string $file)
    {
        $this->file = $file;
    }

    public function open(): void
    {
        // If the file doesn't exist, it's automatically created with 'w'.
        // If it can't be created, an exception is thrown.
        $this->handle = fopen($this->file, 'w');
        if ($this->handle === false) {
            throw new \RuntimeException("The output file could not be created: {$this->file}");
        }
        fwrite($this->handle, '[');
    }

    public function write(array $data): void
    {
        if (!$this->first) {
            fwrite($this->handle, ',');
        }
        fwrite($this->handle, json_encode($data, JSON_UNESCAPED_UNICODE));
        $this->first = false;
    }

    public function close(): void
    {
        fwrite($this->handle, ']');
        fclose($this->handle);
    }
}
