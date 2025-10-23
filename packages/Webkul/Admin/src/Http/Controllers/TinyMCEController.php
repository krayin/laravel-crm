<?php

namespace Webkul\Admin\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Webkul\Core\Traits\Sanitizer;

class TinyMCEController extends Controller
{
    use Sanitizer;

    /**
     * Storage folder path.
     */
    private string $storagePath = 'tinymce';

    /**
     * Upload file from tinymce.
     */
    public function upload(): JsonResponse
    {
        $media = $this->storeMedia();

        if (! empty($media)) {
            return response()->json([
                'location' => $media['file_url'],
            ]);
        }

        return response()->json([]);
    }

    /**
     * Store media.
     */
    public function storeMedia(): array
    {
        if (! request()->hasFile('file')) {
            return [];
        }

        $file = request()->file('file');

        if (! $file instanceof UploadedFile) {
            return [];
        }

        $filename = md5($file->getClientOriginalName().time()).'.'.$file->getClientOriginalExtension();

        $path = $file->storeAs($this->storagePath, $filename);

        $this->sanitizeSVG($path, $file);

        return [
            'file'      => $path,
            'file_name' => $file->getClientOriginalName(),
            'file_url'  => Storage::url($path),
        ];
    }
}
