<?php


namespace App\Dto\Files;


class FileDto
{
    public function __construct(
        private string $directory,
        private string $filename
    )
    {
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getPath(): string
    {
        return "{$this->directory}/{$this->filename}";
    }
}