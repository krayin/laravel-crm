<?php

return [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'refresh_token' => env('GOOGLE_REFRESH_TOKEN'),
    'drive_folder_id' => env('GOOGLE_DRIVE_FOLDER_ID'),
    
    'folders' => [
        'root' => env('GOOGLE_DRIVE_ROOT_FOLDER_ID'),
        'leads' => env('GOOGLE_DRIVE_LEADS_FOLDER_ID'),
        'projects' => env('GOOGLE_DRIVE_PROJECTS_FOLDER_ID'),
        'development' => env('GOOGLE_DRIVE_DEVELOPMENT_FOLDER_ID'),
        'pr' => env('GOOGLE_DRIVE_PR_FOLDER_ID'),
        'scan' => env('GOOGLE_DRIVE_SCAN_FOLDER_ID'),
    ],
    
    'client_numbering' => [
        'format' => '%04d', // Format: 0001, 0002, ... 1315, 1316
    ],
];
