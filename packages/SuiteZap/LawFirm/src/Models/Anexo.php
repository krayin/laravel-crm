<?php

namespace SuiteZap\LawFirm\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Anexo extends Model
{
    protected $table = 'law_processo_anexos';

    protected $fillable = [
        'processo_id',
        'path',
        'nome_original',
        'tipo_mime',
        'tamanho',
    ];

    /**
     * Get the processo that owns the attachment.
     */
    public function processo(): BelongsTo
    {
        return $this->belongsTo(Processo::class);
    }

    /**
     * Get the public URL for the file.
     * Uses Storage::url() to be compatible with both local and S3.
     */
    public function getUrlAttribute(): string
    {
        $path = $this->path;

        // Remove 'public/' from start of path if present (legacy storage)
        if (str_starts_with($path, 'public/')) {
            $path = substr($path, 7);
        }

        // Use Storage::url() for S3 compatibility
        return Storage::url($path);
    }

    /**
     * Get a FontAwesome icon class based on file type.
     */
    public function getIconAttribute(): string
    {
        $mime = $this->tipo_mime;

        if (str_contains($mime, 'pdf')) {
            return 'pdf-icon'; // Adjust based on your icon set usage, e.g., 'fas fa-file-pdf'
        } elseif (str_contains($mime, 'image')) {
            return 'image-icon';
        } elseif (str_contains($mime, 'word') || str_contains($mime, 'document')) {
            return 'word-icon';
        }

        return 'file-icon';
    }

    public function getExtensionAttribute()
    {
        return strtoupper(pathinfo($this->nome_original, PATHINFO_EXTENSION));
    }
}
