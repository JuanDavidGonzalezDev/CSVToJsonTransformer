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
        // Si el archivo no existe, lo crea automáticamente con 'w'.
        // Si no se puede crear, lanza excepción.
        $this->handle = fopen($this->file, 'w');
        if ($this->handle === false) {
            throw new \RuntimeException("No se pudo crear el archivo de salida: {$this->file}");
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
