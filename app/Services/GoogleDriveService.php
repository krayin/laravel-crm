<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Support\Facades\DB;
use Exception;

class GoogleDriveService
{
    protected $client;
    protected $driveService;

    public function __construct()
    {
        $this->client = new Client();
        $this->client->setClientId(config('google.client_id'));
        $this->client->setClientSecret(config('google.client_secret'));
        $this->client->setAccessType('offline');
        $this->client->addScope(Drive::DRIVE);
        
        // Set refresh token and fetch access token
        $refreshToken = config('google.refresh_token');
        $this->client->fetchAccessTokenWithRefreshToken($refreshToken);
        
        $this->driveService = new Drive($this->client);
    }

    /**
     * Create a folder in Google Drive
     *
     * @param string $name Folder name
     * @param string $parentId Parent folder ID
     * @return array ['id' => folder_id, 'url' => folder_url]
     */
    public function createFolder(string $name, string $parentId): array
    {
        $fileMetadata = new DriveFile([
            'name' => $name,
            'mimeType' => 'application/vnd.google-apps.folder',
            'parents' => [$parentId]
        ]);

        $folder = $this->driveService->files->create($fileMetadata, [
            'fields' => 'id, webViewLink',
            'supportsAllDrives' => true  // Support for Shared Drives
        ]);

        return [
            'id' => $folder->id,
            'url' => $folder->webViewLink
        ];
    }

    /**
     * Move folder to a new parent
     *
     * @param string $folderId Folder ID to move
     * @param string $newParentId New parent folder ID
     * @param string $oldParentId Old parent folder ID
     * @return bool
     */
    public function moveFolder(string $folderId, string $newParentId, string $oldParentId): bool
    {
        try {
            $this->driveService->files->update($folderId, new DriveFile(), [
                'addParents' => $newParentId,
                'removeParents' => $oldParentId,
                'fields' => 'id, parents',
                'supportsAllDrives' => true
            ]);
            
            return true;
        } catch (Exception $e) {
            \Log::error('Failed to move folder: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * List files in a folder
     *
     * @param string $folderId Folder ID
     * @return array
     */
    public function listFiles(string $folderId): array
    {
        $query = "'{$folderId}' in parents and trashed=false";
        
        $results = $this->driveService->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name, mimeType, size, createdTime, modifiedTime, webViewLink, thumbnailLink)',
            'orderBy' => 'folder,name',
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true
        ]);

        $files = [];
        foreach ($results->getFiles() as $file) {
            $files[] = [
                'id' => $file->id,
                'name' => $file->name,
                'mimeType' => $file->mimeType,
                'size' => $file->size,
                'createdTime' => $file->createdTime,
                'modifiedTime' => $file->modifiedTime,
                'url' => $file->webViewLink,
                'thumbnail' => $file->thumbnailLink,
                'isFolder' => $file->mimeType === 'application/vnd.google-apps.folder'
            ];
        }

        return $files;
    }

    /**
     * Upload file to Google Drive
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folderId Parent folder ID
     * @return array ['id' => file_id, 'url' => file_url]
     */
    public function uploadFile($file, string $folderId): array
    {
        $fileMetadata = new DriveFile([
            'name' => $file->getClientOriginalName(),
            'parents' => [$folderId]
        ]);

        $content = file_get_contents($file->getRealPath());
        
        $uploadedFile = $this->driveService->files->create($fileMetadata, [
            'data' => $content,
            'mimeType' => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id, webViewLink',
            'supportsAllDrives' => true
        ]);

        return [
            'id' => $uploadedFile->id,
            'url' => $uploadedFile->webViewLink
        ];
    }

    /**
     * Delete file from Google Drive
     *
     * @param string $fileId File ID
     * @return bool
     */
    public function deleteFile(string $fileId): bool
    {
        try {
            $this->driveService->files->delete($fileId);
            return true;
        } catch (Exception $e) {
            \Log::error('Failed to delete file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get folder URL
     *
     * @param string $folderId Folder ID
     * @return string
     */
    public function getFolderUrl(string $folderId): string
    {
        return "https://drive.google.com/drive/folders/{$folderId}";
    }

    /**
     * Get next available client number
     *
     * @return int
     */
    public function getNextClientNumber(): int
    {
        // Get current number from settings table
        $setting = DB::table('system_settings')
            ->where('key', 'next_client_number')
            ->first();

        if (!$setting) {
            // Initialize with 1316 (next after 1315)
            DB::table('system_settings')->insert([
                'key' => 'next_client_number',
                'value' => '1316',
                'created_at' => now(),
                'updated_at' => now()
            ]);
            return 1316;
        }

        $nextNumber = (int) $setting->value;
        
        // Increment for next time
        DB::table('system_settings')
            ->where('key', 'next_client_number')
            ->update([
                'value' => $nextNumber + 1,
                'updated_at' => now()
            ]);

        return $nextNumber;
    }

    /**
     * Format client number according to configuration
     *
     * @param int $number
     * @return string
     */
    public function formatClientNumber(int $number): string
    {
        $format = config('google.client_numbering.format', '%04d');
        return sprintf($format, $number);
    }

    /**
     * Create lead folder name
     *
     * @param int $clientNumber
     * @param string $personName
     * @return string
     */
    public function createLeadFolderName(int $clientNumber, string $personName): string
    {
        return "{$clientNumber} - " . strtoupper($personName);
    }

    /**
     * Create project folder name
     *
     * @param int $clientNumber
     * @param int $projectNumber
     * @param string $projectName
     * @return string
     */
    public function createProjectFolderName(int $clientNumber, int $projectNumber, string $projectName): string
    {
        return "{$clientNumber}.{$projectNumber} - " . strtoupper($projectName);
    }

    /**
     * Check if folder exists by name in parent
     *
     * @param string $folderName
     * @param string $parentId
     * @return string|null Folder ID if exists, null otherwise
     */
    public function findFolderByName(string $folderName, string $parentId): ?string
    {
        $query = "name='{$folderName}' and '{$parentId}' in parents and mimeType='application/vnd.google-apps.folder' and trashed=false";
        
        $results = $this->driveService->files->listFiles([
            'q' => $query,
            'fields' => 'files(id)',
            'pageSize' => 1,
            'supportsAllDrives' => true,
            'includeItemsFromAllDrives' => true
        ]);

        $files = $results->getFiles();
        
        return !empty($files) ? $files[0]->id : null;
    }
}
