<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationsTableSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Resolve default owner for seeded records so they appear in lookups
        // (e.g., quotes person/organization selection respects ACL by owner).
        $defaultOwnerId = DB::table('users')
            ->where('email', 'admin@example.com')
            ->value('id');

        if (! $defaultOwnerId) {
            $defaultOwnerId = DB::table('users')->min('id');
        }

        $rows = [
            ['code' => 'C001', 'name' => 'PT Andalas Food',    'industry' => 'Bakery',               'city' => 'Padang'],
            ['code' => 'C002', 'name' => 'PT Cipta Rasa',      'industry' => 'Bakery',               'city' => 'Jakarta'],
            ['code' => 'C003', 'name' => 'PT Nusantara Steel', 'industry' => 'Metal',                'city' => 'Surabaya'],
            ['code' => 'C004', 'name' => 'PT Prima Plastik',   'industry' => 'Plastik',              'city' => 'Bekasi'],
            ['code' => 'C005', 'name' => 'PT Surya Bakery',    'industry' => 'Bakery',               'city' => 'Semarang'],
            ['code' => 'C006', 'name' => 'PT Delta Pharma',    'industry' => 'Farmasi',              'city' => 'Bandung'],
            ['code' => 'C007', 'name' => 'PT Maju Jaya',       'industry' => 'General Manufacturing','city' => 'Tangerang'],
            ['code' => 'C008', 'name' => 'CV Sentosa',         'industry' => 'General Manufacturing','city' => 'Depok'],
            ['code' => 'C009', 'name' => 'PT Arjuna Metal',    'industry' => 'Metal',                'city' => 'Gresik'],
            ['code' => 'C010', 'name' => 'PT Barokah Logam',   'industry' => 'Metal',                'city' => 'Sidoarjo'],
            ['code' => 'C011', 'name' => 'PT Sinar Elektrik',  'industry' => 'Elektronik',           'city' => 'Cikarang'],
            ['code' => 'C012', 'name' => 'PT Sejahtera Abadi', 'industry' => 'General Manufacturing','city' => 'Karawang'],
        ];

        $inserts = [];

        foreach ($rows as $row) {
            $inserts[] = [
                'name'       => $row['name'],
                'address'    => json_encode([
                    'city'     => $row['city'],
                    'industry' => $row['industry'],
                    'code'     => $row['code'],
                ], JSON_UNESCAPED_SLASHES),
                'user_id'    => $defaultOwnerId,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Idempotent: organizations.name is unique
        DB::table('organizations')->upsert(
            $inserts,
            ['name'],
            ['address', 'user_id', 'updated_at']
        );

        // Also persist EAV attribute_values so UI lookup reads correct values
        // (CustomAttribute merges EAV over model attributes in forms).
        $orgAttributeIds = DB::table('attributes')
            ->where('entity_type', 'organizations')
            ->whereIn('code', ['name', 'address', 'user_id'])
            ->pluck('id', 'code');

        $attributeValues = [];

        foreach ($rows as $row) {
            $orgId = DB::table('organizations')->where('name', $row['name'])->value('id');
            if (! $orgId) {
                continue;
            }

            // name (text)
            if (isset($orgAttributeIds['name'])) {
                $attributeValues[] = [
                    'entity_type'    => 'organizations',
                    'entity_id'      => $orgId,
                    'attribute_id'   => $orgAttributeIds['name'],
                    'text_value'     => $row['name'],
                    'boolean_value'  => null,
                    'integer_value'  => null,
                    'float_value'    => null,
                    'datetime_value' => null,
                    'date_value'     => null,
                    'json_value'     => null,
                ];
            }

            // address (json)
            if (isset($orgAttributeIds['address'])) {
                $attributeValues[] = [
                    'entity_type'    => 'organizations',
                    'entity_id'      => $orgId,
                    'attribute_id'   => $orgAttributeIds['address'],
                    'text_value'     => null,
                    'boolean_value'  => null,
                    'integer_value'  => null,
                    'float_value'    => null,
                    'datetime_value' => null,
                    'date_value'     => null,
                    'json_value'     => json_encode([
                        'city'     => $row['city'],
                        'industry' => $row['industry'],
                        'code'     => $row['code'],
                    ], JSON_UNESCAPED_SLASHES),
                ];
            }

            // user_id (lookup -> integer)
            if (isset($orgAttributeIds['user_id'])) {
                $attributeValues[] = [
                    'entity_type'    => 'organizations',
                    'entity_id'      => $orgId,
                    'attribute_id'   => $orgAttributeIds['user_id'],
                    'text_value'     => null,
                    'boolean_value'  => null,
                    'integer_value'  => $defaultOwnerId,
                    'float_value'    => null,
                    'datetime_value' => null,
                    'date_value'     => null,
                    'json_value'     => null,
                ];
            }
        }

        if (! empty($attributeValues)) {
            DB::table('attribute_values')->upsert(
                $attributeValues,
                ['entity_type', 'entity_id', 'attribute_id'],
                ['text_value', 'boolean_value', 'integer_value', 'float_value', 'datetime_value', 'date_value', 'json_value']
            );
        }
    }
}
