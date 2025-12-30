<?php

namespace SuiteZap\LawFirm\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttributeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->seedLawAreaAttribute();
        $this->seedCpfCnpjAttribute();
    }

    /**
     * Seed the 'law_area' attribute for Leads.
     *
     * @return void
     */
    protected function seedLawAreaAttribute()
    {
        // Check if attribute already exists
        $existingAttribute = DB::table('attributes')
            ->where('code', 'law_area')
            ->where('entity_type', 'leads')
            ->first();

        if ($existingAttribute) {
            $this->command->info('Attribute "law_area" already exists. Skipping...');
            return;
        }

        // Insert the attribute
        $attributeId = DB::table('attributes')->insertGetId([
            'code' => 'law_area',
            'name' => 'Área do Direito',
            'type' => 'select',
            'lookup_type' => null,
            'entity_type' => 'leads',
            'sort_order' => 100,
            'validation' => null,
            'is_required' => false,
            'is_unique' => false,
            'quick_add' => true,
            'is_user_defined' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert attribute options
        $options = [
            ['name' => 'Cível', 'sort_order' => 1],
            ['name' => 'Trabalhista', 'sort_order' => 2],
            ['name' => 'Penal', 'sort_order' => 3],
            ['name' => 'Previdenciário', 'sort_order' => 4],
        ];

        foreach ($options as $option) {
            DB::table('attribute_options')->insert([
                'name' => $option['name'],
                'sort_order' => $option['sort_order'],
                'attribute_id' => $attributeId,
            ]);
        }

        $this->command->info('Attribute "law_area" created successfully with 4 options.');
    }

    /**
     * Seed the 'cpf_cnpj' attribute for Persons.
     *
     * @return void
     */
    protected function seedCpfCnpjAttribute()
    {
        // Check if attribute already exists
        $existingAttribute = DB::table('attributes')
            ->where('code', 'cpf_cnpj')
            ->where('entity_type', 'persons')
            ->first();

        if ($existingAttribute) {
            $this->command->info('Attribute "cpf_cnpj" already exists. Skipping...');
            return;
        }

        // Insert the attribute
        DB::table('attributes')->insert([
            'code' => 'cpf_cnpj',
            'name' => 'CPF/CNPJ',
            'type' => 'text',
            'lookup_type' => null,
            'entity_type' => 'persons',
            'sort_order' => 100,
            'validation' => null,
            'is_required' => false,
            'is_unique' => false,
            'quick_add' => true,
            'is_user_defined' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('Attribute "cpf_cnpj" created successfully.');
    }
}
