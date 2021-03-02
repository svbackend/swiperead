<?php

namespace App\Service\Files;

use App\Dto\Files\RemoteFileDto;
use App\Exception\Files\FileDownloadingException;
use App\ValueObject\User\UserId;

class UserFilesManager extends FilesManager
{
    protected function getPathPrefix(): string
    {
        return 'user';
    }

    public function saveUserAvatar(UserId $userId, string $url): RemoteFileDto
    {
        try {
            return $this->saveFromUrlWithFilename('avatar.jpeg', $url, $userId->getValue());
        } catch (FileDownloadingException $fileDownloadingException) {
            // todo capture to sentry
            return RemoteFileDto::fromUrl('https://placeimg.com/100/100/any');
        }
    }

    public function getUserAvatar(UserId $userId): RemoteFileDto
    {
        $dir = $userId->getValue();
        $filename = 'avatar.jpeg';
        $path = $this->getFilepath($dir, $filename);
        $url = $this->prependBaseUrl($path);
        return new RemoteFileDto($url, $dir, $filename);
    }
}