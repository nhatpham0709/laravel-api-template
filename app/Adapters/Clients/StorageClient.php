<?php

namespace App\Adapters\Clients;

/**
 * Represents methods interacting with the StorageClient service.
 */
interface StorageClient
{
    /**
     * Get image url.
     *
     * @param string $path Storage path.
     * @param string $name File name.
     * @return string
     */
    public function getImageUrl(string $path, string $name): ?string;

    /**
     * Get image url.
     *
     * @param string|null $pathName Storage path.
     * @return string|null
     */
    public function getImageUrlByPathName(?string $pathName): ?string;

    /**
     * Get image url.
     *
     * @param string|null $pathName Storage path.
     * @return string|null
     */
    public function getImageUrlByPathNamePrivate(?string $pathName): ?string;

    /**
     * Get temporary url of image on AWS S3.
     *
     * @param string $path Storage path.
     * @param string $name File name.
     * @return string
     */
    public function getS3TemporaryUrl(string $path, string $name): string;

    /**
     * Upload file.
     *
     * @param object $file File object.
     * @param string $path Storage path.
     * @param boolean $isRename Is rename file.
     * @return array
     */
    public function uploadFile(object $file, string $path, bool $isRename = true): array;

    /**
     * Delete file.
     *
     * @param string $name File name.
     * @param string $path Storage path.
     * @return bool
     */
    public function deleteFile(string $name, string $path): bool;

    /**
     * Delete file.
     *
     * @param string $url.
     * @return bool
     */
    public function deleteFileByUrl(string $url): bool;
}
