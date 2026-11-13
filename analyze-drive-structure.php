#!/usr/bin/env php
<?php

require __DIR__.'/vendor/autoload.php';

use Google\Client;
use Google\Service\Drive;

// Загрузка конфигурации
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Настройка Google Client
$client = new Client();
$client->setClientId($_ENV['GOOGLE_CLIENT_ID']);
$client->setClientSecret($_ENV['GOOGLE_CLIENT_SECRET']);
$client->setAccessType('offline');
$client->setScopes([Drive::DRIVE]);

// Установка refresh token
$client->refreshToken($_ENV['GOOGLE_REFRESH_TOKEN']);

// Создание сервиса Drive
$driveService = new Drive($client);

// ID корневой папки компании
$rootFolderId = '0AL0zGtXFlzoiUk9PVA';

echo "🔍 Analyzing Google Drive structure...\n\n";
echo "Root Folder ID: {$rootFolderId}\n";
echo str_repeat("=", 60) . "\n\n";

function listFolderContents($service, $folderId, $level = 0) {
    $indent = str_repeat("  ", $level);
    
    try {
        $query = "'{$folderId}' in parents and trashed=false";
        $results = $service->files->listFiles([
            'q' => $query,
            'fields' => 'files(id, name, mimeType, createdTime, modifiedTime)',
            'orderBy' => 'folder,name'
        ]);
        
        $files = $results->getFiles();
        
        if (empty($files)) {
            echo "{$indent}📁 (empty)\n";
            return;
        }
        
        foreach ($files as $file) {
            $isFolder = $file->getMimeType() === 'application/vnd.google-apps.folder';
            $icon = $isFolder ? '📁' : '📄';
            
            echo "{$indent}{$icon} {$file->getName()}\n";
            echo "{$indent}   ID: {$file->getId()}\n";
            echo "{$indent}   Created: {$file->getCreatedTime()}\n";
            
            // Рекурсивно показываем содержимое папок (только первый уровень)
            if ($isFolder && $level < 2) {
                listFolderContents($service, $file->getId(), $level + 1);
            }
            
            echo "\n";
        }
    } catch (Exception $e) {
        echo "{$indent}❌ Error: {$e->getMessage()}\n";
    }
}

// Анализируем структуру
listFolderContents($driveService, $rootFolderId);

echo str_repeat("=", 60) . "\n";
echo "✅ Analysis complete!\n";
