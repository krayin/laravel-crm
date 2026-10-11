<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Inbound replies to a reply used to be attached to that reply instead of the thread root,
     * which hid them from both the mail list and the thread view. Every nested email is moved
     * up to be a direct child of the root of its thread.
     */
    public function up(): void
    {
        $parentIds = DB::table('emails')
            ->whereNotNull('parent_id')
            ->pluck('parent_id', 'id')
            ->all();

        $rootIds = [];

        foreach ($parentIds as $id => $parentId) {
            $rootId = $parentId;

            $visitedIds = [$id];

            while (isset($parentIds[$rootId]) && ! in_array($rootId, $visitedIds)) {
                $visitedIds[] = $rootId;

                $rootId = $parentIds[$rootId];
            }

            if ($rootId != $parentId) {
                $rootIds[$rootId][] = $id;
            }
        }

        foreach ($rootIds as $rootId => $ids) {
            foreach (array_chunk($ids, 500) as $chunk) {
                DB::table('emails')
                    ->whereIn('id', $chunk)
                    ->update(['parent_id' => $rootId]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // The original nesting is not kept, and the flat threads are what the application expects.
    }
};
