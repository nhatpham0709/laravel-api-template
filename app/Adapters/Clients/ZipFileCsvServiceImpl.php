<?php

namespace App\Adapters\Clients;

use App\Exceptions\ApiException;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use ZipArchive;

/**
 * zip file csv and upload to S3 service
 */
class ZipFileCsvServiceImpl implements ZipFileCsvService
{
    /**
     * zip and upload to s3
     * @param string|null $zipTmpFile
     * @param string|null $password
     * @param string|null $csvFileForZip
     * @param string|null $fileCsvName
     * @param string|null $fileCsvZipName
     * @param string|null $folderUploadZip
     * @return string|null
     */
    public function zipFileCsvAndUpload(?string $zipTmpFile, ?string $password, ?string $csvFileForZip, ?string $fileCsvName, ?string $fileCsvZipName, ?string $folderUploadZip): ?string
    {
        $zip = new ZipArchive();
        $zipStatus = $zip->open(storage_path($zipTmpFile), ZipArchive::CREATE);
        if ($zipStatus !== true) {
            throw ApiException::badRequest(trans('general.zip_csv.create_zip_export_csv_failed'));
        }
        if (!$zip->setPassword($password)) {
            throw ApiException::badRequest(trans('general.zip_csv.create_password_zip_csv_failed'));
        }
        if (!$zip->addFile(storage_path($csvFileForZip), $fileCsvName)) {
            throw ApiException::badRequest(trans('general.zip_csv.export_csv_failed'));
        }
        if (!$zip->setEncryptionName($fileCsvName, ZipArchive::EM_AES_256)) {
            throw ApiException::badRequest(trans('general.zip_csv.set_encrypt_password_zip_csv_failed'));
        }
        $zip->close();
        $fileZip = new UploadedFile(storage_path($zipTmpFile), $fileCsvZipName);
        $fileZipStorage = $this->uploadFile((object)$fileZip, $folderUploadZip);
        Storage::disk('local')->delete(sprintf('%s/%s', $folderUploadZip, $fileCsvName));
        Storage::disk('local')->delete(sprintf('%s/%s', $folderUploadZip, $fileCsvZipName));
        $url = $this->getImageUrl($fileZipStorage['path'], $fileZipStorage['name']);
        if (config('filesystems.default') === 's3') {
            $url = $this->getS3TemporaryUrl($fileZipStorage['path'], $fileZipStorage['name']);
        }
        return $url;
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
        return Storage::disk(config('filesystems.default'))->temporaryUrl(
            $this->generateFileName($path, $name),
            Carbon::now()->addMinutes(30)
        );
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

        return Storage::disk(config('filesystems.default'))->url($key);
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
            $dataInfo = array();
            $dataInfo['path'] = $path;
            $dataInfo['name'] = $this->getFileName($file, $isRename);
            $key = $this->generateFileName($path, $dataInfo['name']);
            $fileContent = file_get_contents($file);
            if (!$fileContent) {
                throw new RuntimeException('The file is invalid.');
            }
            Storage::disk(config('filesystems.default'))->put($key, $fileContent);
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
}
