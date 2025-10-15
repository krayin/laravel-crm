<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonsFromOrganizationsSeeder extends Seeder
{
    public function run(): void
    {
        $now = Carbon::now();

        // Assign an owner so seeded persons pass ACL filters in lookups
        // like the quote person selector (which filters by authorized user_ids).
        $defaultOwnerId = DB::table('users')
            ->where('email', 'admin@example.com')
            ->value('id');

        if (! $defaultOwnerId) {
            $defaultOwnerId = DB::table('users')->min('id');
        }

        $orgs = [
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

        foreach ($orgs as $i => $org) {
            $orgId = DB::table('organizations')->where('name', $org['name'])->value('id');
            if (! $orgId) {
                // Skip if organization not present (should be seeded first)
                continue;
            }

            // Person dummy data derived from organization
            $personName = 'PIC '.$org['name'];
            $slug       = Str::slug($org['name']);
            $email      = 'pic.'.$slug.'.'.strtolower($org['code']).'@example.com';
            $phone      = '0813'.str_pad((string)($i + 2020), 7, '0', STR_PAD_LEFT);

            $inserts[] = [
                'name'             => $personName,
                'emails'           => json_encode([[ 'value' => $email, 'label' => 'work' ]], JSON_UNESCAPED_SLASHES),
                'contact_numbers'  => json_encode([[ 'value' => $phone, 'label' => 'mobile' ]], JSON_UNESCAPED_SLASHES),
                'organization_id'  => $orgId,
                'job_title'        => 'PIC',
                'user_id'          => $defaultOwnerId,
                // Align with unique_id convention: user_id|organization_id|email|phone (omit null user_id)
                'unique_id'        => $orgId.'|'.$email.'|'.$phone,
                'created_at'       => $now,
                'updated_at'       => $now,
            ];
        }

        DB::table('persons')->upsert(
            $inserts,
            ['unique_id'],
            ['name', 'emails', 'contact_numbers', 'organization_id', 'job_title', 'user_id', 'updated_at']
        );

        // Persist EAV attribute_values for persons so UI lookup shows correct data
        $personAttributeIds = DB::table('attributes')
            ->where('entity_type', 'persons')
            ->whereIn('code', ['name', 'emails', 'contact_numbers', 'job_title', 'user_id', 'organization_id'])
            ->pluck('id', 'code');

        $attributeValues = [];

        foreach ($inserts as $row) {
            $personId = DB::table('persons')->where('unique_id', $row['unique_id'])->value('id');
            if (! $personId) {
                continue;
            }

            // name (text)
            if (isset($personAttributeIds['name'])) {
                $attributeValues[] = [
                    'entity_type'    => 'persons',
                    'entity_id'      => $personId,
                    'attribute_id'   => $personAttributeIds['name'],
                    'text_value'     => $row['name'],
                    'boolean_value'  => null,
                    'integer_value'  => null,
                    'float_value'    => null,
                    'datetime_value' => null,
                    'date_value'     => null,
                    'json_value'     => null,
                ];
            }

            // emails (json)
            if (isset($personAttributeIds['emails'])) {
                $attributeValues[] = [
                    'entity_type'    => 'persons',
                    'entity_id'      => $personId,
                    'attribute_id'   => $personAttributeIds['emails'],
                    'text_value'     => null,
                    'boolean_value'  => null,
                    'integer_value'  => null,
                    'float_value'    => null,
                    'datetime_value' => null,
                    'date_value'     => null,
                    'json_value'     => $row['emails'],
                ];
            }

            // contact_numbers (json)
            if (isset($personAttributeIds['contact_numbers'])) {
                $attributeValues[] = [
                    'entity_type'    => 'persons',
                    'entity_id'      => $personId,
                    'attribute_id'   => $personAttributeIds['contact_numbers'],
                    'text_value'     => null,
                    'boolean_value'  => null,
                    'integer_value'  => null,
                    'float_value'    => null,
                    'datetime_value' => null,
                    'date_value'     => null,
                    'json_value'     => $row['contact_numbers'],
                ];
            }

            // job_title (text)
            if (isset($personAttributeIds['job_title'])) {
                $attributeValues[] = [
                    'entity_type'    => 'persons',
                    'entity_id'      => $personId,
                    'attribute_id'   => $personAttributeIds['job_title'],
                    'text_value'     => $row['job_title'],
                    'boolean_value'  => null,
                    'integer_value'  => null,
                    'float_value'    => null,
                    'datetime_value' => null,
                    'date_value'     => null,
                    'json_value'     => null,
                ];
            }

            // user_id (lookup -> integer)
            if (isset($personAttributeIds['user_id'])) {
                $attributeValues[] = [
                    'entity_type'    => 'persons',
                    'entity_id'      => $personId,
                    'attribute_id'   => $personAttributeIds['user_id'],
                    'text_value'     => null,
                    'boolean_value'  => null,
                    'integer_value'  => $defaultOwnerId,
                    'float_value'    => null,
                    'datetime_value' => null,
                    'date_value'     => null,
                    'json_value'     => null,
                ];
            }

            // organization_id (lookup -> integer)
            if (isset($personAttributeIds['organization_id'])) {
                $attributeValues[] = [
                    'entity_type'    => 'persons',
                    'entity_id'      => $personId,
                    'attribute_id'   => $personAttributeIds['organization_id'],
                    'text_value'     => null,
                    'boolean_value'  => null,
                    'integer_value'  => $row['organization_id'],
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
