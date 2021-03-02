<?php

namespace App\Service\Files;

use App\Dto\Files\FileDto;
use App\Dto\Files\RemoteFileDto;
use App\Exception\Files\FileDownloadingException;
use App\Service\Proxy\ProxyProvider;
use App\Utils\Env;
use App\Utils\Retry;
use Aws\S3\S3Client;
use GuzzleHttp\Client;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use League\Flysystem\Filesystem;
use League\Flysystem\InMemory\InMemoryFilesystemAdapter;
use League\Flysystem\Visibility;
use Psr\Log\LoggerInterface;

abstract class FilesManager
{
    protected Filesystem $fs;

    public function __construct(
        protected LoggerInterface $logger,
        private Client $client,
        private ProxyProvider $proxyProvider
    )
    {
        // todo move to config and overwrite for test env (it will add this service to container cache)
        if (Env::get('APP_ENV') === 'test') {
            $adapter = new InMemoryFilesystemAdapter();
        } else {
            $c = new S3Client([
                'credentials' => [
                    'key' => Env::get('S3_STORAGE_KEY'),
                    'secret' => Env::get('S3_STORAGE_SECRET'),
                ],
                'version' => Env::get('S3_STORAGE_VERSION'),
                'region' => Env::get('S3_STORAGE_REGION')
            ]);
            $adapter = new AwsS3V3Adapter($c, Env::get('S3_STORAGE_BUCKET'));
        }

        $this->fs = new Filesystem($adapter);
    }

    private function generateFilename(): string
    {
        return uniqid('', true);
    }

    abstract protected function getPathPrefix(): string;

    /**
     * If you know the expected extension of the file from url then pass it as a parameter
     * lets say you expect to download an image then pass jpg or png as ext, so we won't try to guess it by url
     * Because it could be detected incorrectly if url doesn't contain extension
     * like "site.com/image123" detected ext would be = "com/image123"
     * @throws
     * @see getFileExtensionFromUrl
     */
    protected function saveFromUrl(
        string $url, string $toDirectory, ?string $ext = null
    ): RemoteFileDto
    {
        $ext = $ext ?? $this->getFileExtensionFromUrl($url);
        $filename = "{$this->generateFilename()}.{$ext}";
        return $this->saveFromUrlWithFilename(
            $filename,
            $url,
            $toDirectory
        );
    }

    protected function getFilepath(string $dir, string $filename) {
        return sprintf('%s/%s/%s', static::getPathPrefix(), $dir, $filename);
    }

    /**
     * If you know the expected extension of the file from url then pass it as a parameter
     * lets say you expect to download an image then pass jpg or png as ext, so we won't try to guess it by url
     * Because it could be detected incorrectly if url doesn't contain extension
     * like "site.com/image123" detected ext would be = "com/image123"
     * @throws
     * @see getFileExtensionFromUrl
     */
    protected function saveFromUrlWithFilename(
        string $filename, string $url, string $toDirectory
    ): RemoteFileDto
    {
        $path = $this->getFilepath($toDirectory, $filename);

        $response = Retry::once(
            fn() => $this->client->get($url, [
                'proxy' => $this->proxyProvider->getRandomProxy()
            ])
        );
        $content = $response->getBody()->getContents();

        if (!$content) {
            throw new FileDownloadingException("Error during file downloading from {$url}");
        }

        $this->fs->write($path, $content, [
            'visibility' => Visibility::PUBLIC
        ]);
        $url = $this->prependBaseUrl($path);
        return new RemoteFileDto($url, $toDirectory, $filename);
    }

    /** @throws */
    protected function deleteFile(FileDto $file): void
    {
        $path = sprintf('%s/%s', static::getPathPrefix(), $file->getPath());
        $this->fs->delete($path);
    }

    private function getFileExtensionFromUrl(string $url): string
    {
        $url = explode('?', $url)[0];
        $result = explode('.', $url);
        return end($result);
    }

    protected function prependBaseUrl(string $path): string
    {
        return sprintf(
            'https://%s.s3.%s.amazonaws.com/%s',
            Env::get('S3_STORAGE_BUCKET'),
            Env::get('S3_STORAGE_REGION'),
            $path
        );
    }
}