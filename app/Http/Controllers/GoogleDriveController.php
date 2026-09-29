<?php

namespace App\Http\Controllers;

use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Webkul\Lead\Repositories\LeadRepository;
use Illuminate\Support\Facades\DB;

class GoogleDriveController extends Controller
{
    protected $googleDriveService;
    protected $leadRepository;

    public function __construct(
        GoogleDriveService $googleDriveService,
        LeadRepository $leadRepository
    ) {
        $this->googleDriveService = $googleDriveService;
        $this->leadRepository = $leadRepository;
    }

    /**
     * Create Google Drive folder for lead
     *
     * @param int $id Lead ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function createLeadFolder($id)
    {
        \Log::info('createLeadFolder called for lead ID: ' . $id);
        
        try {
            $lead = $this->leadRepository->findOrFail($id);
            
            \Log::info('Lead found', ['lead_id' => $lead->id, 'title' => $lead->title]);
            
            // Check if folder already exists
            if ($lead->google_drive_folder_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Folder already exists for this lead',
                    'folder_url' => $lead->google_drive_folder_url
                ], 400);
            }

            // Get or assign client number
            if (!$lead->client_number) {
                $clientNumber = $this->googleDriveService->getNextClientNumber();
                $lead->client_number = $clientNumber;
            } else {
                $clientNumber = $lead->client_number;
            }

            // Get person name
            $personName = $lead->person->name ?? 'Unknown';
            
            // Create folder name
            $folderName = $this->googleDriveService->createLeadFolderName($clientNumber, $personName);
            
            // Check if folder with this name already exists
            $leadsFolderId = config('google.folders.leads');
            $existingFolderId = $this->googleDriveService->findFolderByName($folderName, $leadsFolderId);
            
            if ($existingFolderId) {
                // Folder exists, link it
                $lead->google_drive_folder_id = $existingFolderId;
                $lead->google_drive_folder_url = $this->googleDriveService->getFolderUrl($existingFolderId);
                $lead->folder_created_at = now();
                $lead->title = $folderName;  // Update title to match folder name
                $lead->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Existing folder linked successfully',
                    'folder_id' => $existingFolderId,
                    'folder_url' => $lead->google_drive_folder_url,
                    'client_number' => $clientNumber,
                    'new_title' => $folderName
                ]);
            }
            
            // Create new folder
            $folder = $this->googleDriveService->createFolder($folderName, $leadsFolderId);
            
            // Update lead
            $lead->google_drive_folder_id = $folder['id'];
            $lead->google_drive_folder_url = $folder['url'];
            $lead->folder_created_at = now();
            $lead->title = $folderName;  // Update title to match folder name
            $lead->save();
            
            return response()->json([
                'success' => true,
                'message' => 'Folder created successfully',
                'folder_id' => $folder['id'],
                'folder_url' => $folder['url'],
                'client_number' => $clientNumber,
                'new_title' => $folderName
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create lead folder: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create folder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Move lead folder to Projects directory
     *
     * @param int $id Lead ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function moveToProjects($id)
    {
        try {
            $lead = $this->leadRepository->findOrFail($id);
            
            if (!$lead->google_drive_folder_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No folder exists for this lead'
                ], 400);
            }
            
            $leadsFolderId = config('google.folders.leads');
            $projectsFolderId = config('google.folders.projects');
            
            $success = $this->googleDriveService->moveFolder(
                $lead->google_drive_folder_id,
                $projectsFolderId,
                $leadsFolderId
            );
            
            if ($success) {
                $lead->folder_moved_at = now();
                $lead->save();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Folder moved to Projects successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to move folder'
            ], 500);
            
        } catch (\Exception $e) {
            \Log::error('Failed to move folder: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to move folder: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * List files in lead folder
     *
     * @param int $id Lead ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function listFiles($id)
    {
        try {
            $lead = $this->leadRepository->findOrFail($id);
            
            if (!$lead->google_drive_folder_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No folder exists for this lead',
                    'files' => []
                ]);
            }
            
            $files = $this->googleDriveService->listFiles($lead->google_drive_folder_id);
            
            return response()->json([
                'success' => true,
                'files' => $files
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to list files: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to list files: ' . $e->getMessage(),
                'files' => []
            ], 500);
        }
    }

    /**
     * Upload file to lead folder
     *
     * @param int $id Lead ID
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadFile($id, Request $request)
    {
        try {
            $lead = $this->leadRepository->findOrFail($id);
            
            if (!$lead->google_drive_folder_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No folder exists for this lead'
                ], 400);
            }
            
            $request->validate([
                'file' => 'required|file|max:51200' // 50MB max
            ]);
            
            $file = $request->file('file');
            $uploadedFile = $this->googleDriveService->uploadFile($file, $lead->google_drive_folder_id);
            
            return response()->json([
                'success' => true,
                'message' => 'File uploaded successfully',
                'file' => $uploadedFile
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to upload file: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete file from Drive
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteFile(Request $request)
    {
        try {
            $request->validate([
                'file_id' => 'required|string'
            ]);
            
            $success = $this->googleDriveService->deleteFile($request->file_id);
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'File deleted successfully'
                ]);
            }
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file'
            ], 500);
            
        } catch (\Exception $e) {
            \Log::error('Failed to delete file: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete file: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Create project for lead
     *
     * @param int $id Lead ID
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createProject($id, Request $request)
    {
        try {
            $lead = $this->leadRepository->findOrFail($id);
            
            if (!$lead->google_drive_folder_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No folder exists for this lead. Create lead folder first.'
                ], 400);
            }
            
            $request->validate([
                'project_name' => 'required|string|max:255'
            ]);
            
            // Get next project number
            $projectNumber = DB::table('lead_projects')
                ->where('lead_id', $lead->id)
                ->max('project_number') + 1;
            
            if (!$projectNumber) {
                $projectNumber = 1;
            }
            
            // Create project folder name
            $folderName = $this->googleDriveService->createProjectFolderName(
                $lead->client_number,
                $projectNumber,
                $request->project_name
            );
            
            // Create folder
            $folder = $this->googleDriveService->createFolder($folderName, $lead->google_drive_folder_id);
            
            // Save project
            $project = DB::table('lead_projects')->insertGetId([
                'lead_id' => $lead->id,
                'project_number' => $projectNumber,
                'project_name' => $request->project_name,
                'google_drive_folder_id' => $folder['id'],
                'google_drive_folder_url' => $folder['url'],
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'project' => [
                    'id' => $project,
                    'project_number' => $projectNumber,
                    'project_name' => $request->project_name,
                    'folder_id' => $folder['id'],
                    'folder_url' => $folder['url']
                ]
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to create project: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to create project: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get projects for lead
     *
     * @param int $id Lead ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getProjects($id)
    {
        try {
            $projects = DB::table('lead_projects')
                ->where('lead_id', $id)
                ->orderBy('project_number')
                ->get();
            
            return response()->json([
                'success' => true,
                'projects' => $projects
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Failed to get projects: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to get projects: ' . $e->getMessage(),
                'projects' => []
            ], 500);
        }
    }
}
