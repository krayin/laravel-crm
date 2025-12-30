<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Insert custom field for Leads: Área do Direito (Law Area)
        $lawAreaAttributeId = DB::table('attributes')->insertGetId([
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

        // Insert options for Law Area field
        $lawAreaOptions = [
            ['name' => 'Cível', 'sort_order' => 1],
            ['name' => 'Trabalhista', 'sort_order' => 2],
            ['name' => 'Penal', 'sort_order' => 3],
            ['name' => 'Previdenciário', 'sort_order' => 4],
        ];

        foreach ($lawAreaOptions as $option) {
            DB::table('attribute_options')->insert([
                'name' => $option['name'],
                'sort_order' => $option['sort_order'],
                'attribute_id' => $lawAreaAttributeId,
            ]);
        }

        // Insert custom field for Persons: CPF/CNPJ
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
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove custom field for Leads: law_area
        $lawAreaAttribute = DB::table('attributes')
            ->where('code', 'law_area')
            ->where('entity_type', 'leads')
            ->first();

        if ($lawAreaAttribute) {
            // Delete associated options first (cascade should handle this, but being explicit)
            DB::table('attribute_options')
                ->where('attribute_id', $lawAreaAttribute->id)
                ->delete();

            // Delete the attribute
            DB::table('attributes')
                ->where('id', $lawAreaAttribute->id)
                ->delete();
        }

        // Remove custom field for Persons: cpf_cnpj
        DB::table('attributes')
            ->where('code', 'cpf_cnpj')
            ->where('entity_type', 'persons')
            ->delete();
    }
};
