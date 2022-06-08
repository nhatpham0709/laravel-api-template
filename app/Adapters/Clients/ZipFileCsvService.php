<?php

namespace App\Adapters\Clients;

/**
 * zip file csv and upload to S3 service
 */
interface ZipFileCsvService
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
    public function zipFileCsvAndUpload(?string $zipTmpFile, ?string $password, ?string $csvFileForZip, ?string $fileCsvName, ?string $fileCsvZipName, ?string $folderUploadZip): ?string;
}
