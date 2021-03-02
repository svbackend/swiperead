<?php


namespace App\Dto\Files;


use App\Entity\Files\RemoteFile;

class RemoteFileDto extends FileDto
{
    public function __construct(
        private string $url,
        ?string $directory = null,
        ?string $filename = null,
    )
    {
        $directory = $directory ?? '';
        $filename = $filename ?? '';
        parent::__construct($directory, $filename);
    }

    public static function fromEmbeddable(RemoteFile $file): self
    {
        return new self($file->getUrl(), $file->getDirectory(), $file->getFilename());
    }

    public static function fromUrl(string $url): self
    {
        return new self($url);
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function isUploaded(): bool
    {
        return !empty($this->getDirectory()) && !empty($this->getFilename());
    }
}