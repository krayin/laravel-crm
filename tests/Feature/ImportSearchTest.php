<?php

use Illuminate\Support\Facades\DB;

it('filters the datagrid by search term', function () {
    $admin = getDefaultAdmin();

    // Clean up before test
    DB::table('imports')->delete();

    // Create a matching import
    DB::table('imports')->insert([
        'state' => 'completed',
        'type' => 'contacts',
        'action' => 'append',
        'validation_strategy' => 'stop-on-errors',
        'allowed_errors' => 10,
        'processed_rows_count' => 5,
        'invalid_rows_count' => 0,
        'errors_count' => 0,
        'field_separator' => ',',
        'file_path' => 'import/target-file-xyx1.csv',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Create a non-matching import
    DB::table('imports')->insert([
        'state' => 'pending',
        'type' => 'products',
        'action' => 'append',
        'validation_strategy' => 'stop-on-errors',
        'allowed_errors' => 10,
        'processed_rows_count' => 5,
        'invalid_rows_count' => 0,
        'errors_count' => 0,
        'field_separator' => ',',
        'file_path' => 'import/other-file-zzz.csv',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    test()->actingAs($admin)
        ->getJson(route('admin.settings.data_transfer.imports.index', [
            'filters' => [
                'all' => ['target-file'],
            ]
        ]))
        ->assertJsonFragment([
            'file_path' => 'import/target-file-xyx1.csv'
        ])
        ->assertJsonMissing([
            'file_path' => 'import/other-file-zzz.csv'
        ]);
});
