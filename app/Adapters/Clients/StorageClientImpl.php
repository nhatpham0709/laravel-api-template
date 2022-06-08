<?php

namespace App\Adapters\Clients;

use App\Exceptions\ApiException;
use Carbon\Carbon;
use Exception;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Represents methods interacting with the StorageClient service.
 */
class StorageClientImpl implements StorageClient
{
    /**
     * @var Filesystem
     */
    private Filesystem $storageClient;

    /**
     * StorageClientImpl constructor.
     */
    public function __construct()
    {
        $this->setStorageClient(config('filesystems.default'));
    }

    /**
     * Set config StorageClient.
     *
     * @param string $disk Filesystem disk.
     * @return void
     */
    private function setStorageClient(string $disk): void
    {
        $disks = config('filesystems.disks');
        
        if (empty($disks[$disk])) {
            throw ApiException::serviceUnavailable();
        }

        $this->storageClient = Storage::disk($disk);
    }

    /**
     * Get image url.
     *
     * @param string $path Storage path.
     * @param string $name File name.
     * @return string
     */
    public function getImageUrl(string $path, string $name): string
    {
        $key = $this->generateFileName($path, $name);

        return $this->storageClient->url($key);
    }

    /**
     * Get image url using path.
     *
     * @param string|null $pathName Storage pathName.
     * @return string|null
     */
    public function getImageUrlByPathName(?string $pathName): ?string
    {
        if (!$pathName) {
            return null;
        }

        return $this->storageClient->url($pathName);
    }

    /**
     * Get image url using path.
     *
     * @param string|null $pathName Storage pathName.
     * @return string|null
     */
    public function getImageUrlByPathNamePrivate(?string $pathName): ?string
    {
        if (!$pathName) {
            return null;
        }

        return $this->storageClient->temporaryUrl(
            $pathName,
            Carbon::now()->addMinutes(30)
        );
    }

    /**
     * Get temporary url of image on AWS S3.
     *
     * @param string $path Storage path.
     * @param string $name File name.
     * @return string
     */
    public function getS3TemporaryUrl(string $path, string $name): string
    {
        return $this->storageClient->temporaryUrl(
            $this->generateFileName($path, $name),
            Carbon::now()->addMinutes(30)
        );
    }

    /**
     * Generate file name from path and end name.
     *
     * @param string $path Storage path.
     * @param string $name File name.
     * @return string
     */
    private function generateFileName(string $path, string $name): string
    {
        return sprintf('%s/%s', $path, $name);
    }

    /**
     * Upload file.
     *
     * @param object $file File object.
     * @param string $path Storage path.
     * @param boolean $isRename Is rename file.
     * @return array
     */
    public function uploadFile(object $file, string $path, bool $isRename = true): array
    {
        try {
            $dataInfo = $this->getFileInfo($file);
            $dataInfo['path'] = $path;
            $dataInfo['name'] = $this->getFileName($file, $isRename);
            $key = $this->generateFileName($path, $dataInfo['name']);
            $fileContent = file_get_contents($file);
            if (!$fileContent) {
                throw new RuntimeException('The file is invalid.');
            }

            $this->storageClient->put($key, $fileContent);

            return $dataInfo;
        } catch (Exception $e) {
            Log::error('[ERROR_UPLOAD_FILE] =>' . $e->getMessage());

            return [];
        }//end try
    }

    /**
     * Get file name.
     *
     * @param object $file File object.
     * @param boolean $isRename Is rename file.
     * @return string
     */
    private function getFileName(object $file, bool $isRename = true): string
    {
        $fileName = $file->getClientOriginalName();
        if ($isRename) {
            $fileName = sprintf('%s.%s', Str::uuid(), $file->getClientOriginalExtension());
        }

        return $fileName;
    }

    /**
     * Get file info.
     *
     * @param object $file File object.
     * @return array
     */
    private function getFileInfo(object $file): array
    {
        $imageSize = getimagesize($file);
        $size = null;
        if ($imageSize) {
            $size = $imageSize[0] . 'x' . $imageSize[1];
        }

        return [
            'size' => $size,
            'type' => $file->getClientOriginalExtension()
        ];
    }

    /**
     * Delete file.
     *
     * @param string $name File name.
     * @param string $path Storage path.
     * @return bool
     */
    public function deleteFile(string $name, string $path): bool
    {
        try {
            $key = $this->generateFileName($path, $name);

            return $this->storageClient->delete($key);
        } catch (\Exception $e) {
            \Log::error('[ERROR_DELETE_FILE] =>' . $e->getMessage());

            return false;
        }
    }

    /**
     * Delete file.
     *
     * @param string $url.
     * @return bool
     */
    public function deleteFileByUrl(string $url): bool
    {
        try {
            return $this->storageClient->delete($url);
        } catch (\Exception $e) {
            \Log::error('[ERROR_DELETE_FILE] =>' . $e->getMessage());

            return false;
        }
    }
}
