<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Storage;
use Webkul\DataTransfer\Helpers\Import;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Import source files and error reports used to be written to the `public` disk, which is
     * linked into the web root, so anyone could download them without signing in. They are now
     * kept on the private disk; this moves the files earlier imports left behind. Each file keeps
     * its relative path, so the `file_path` and `error_file_path` columns stay valid.
     */
    public function up(): void
    {
        $public = Storage::disk('public');

        $private = Storage::disk(Import::STORAGE_DISK);

        /**
         * When both disks point at the same directory there is nothing to move, and deleting the
         * "old" copy would delete the only one.
         */
        if (rtrim($public->path(''), '/') === rtrim($private->path(''), '/')) {
            return;
        }

        foreach ($public->allFiles(Import::STORAGE_DIRECTORY) as $path) {
            if (! $private->exists($path)) {
                $stream = $public->readStream($path);

                $private->writeStream($path, $stream);

                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            /**
             * Only drop the public copy once the private one is in place, so a failed copy never
             * loses the file; the migration can simply be run again.
             */
            if ($private->exists($path)) {
                $public->delete($path);
            }
        }

        if (empty($public->allFiles(Import::STORAGE_DIRECTORY))) {
            $public->deleteDirectory(Import::STORAGE_DIRECTORY);
        }
    }

    /**
     * Reverse the migrations.
     *
     * Intentionally left empty: moving the files back would expose them publicly again.
     */
    public function down(): void {}
};
