<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class RemoveOrganizationRelationshipFromPersons extends Migration
{
    /**
     * Store organization relationships for restoration in down()
     */
    private $relationships = [];

    /**
     * Run the migrations.
     */
    public function up()
    {
        // Store existing relationships before deletion
        $this->relationships = DB::table('persons')
            ->whereNotNull('organization_id')
            ->select(['id', 'organization_id'])
            ->get()
            ->toArray();

        Schema::table('persons', function (Blueprint $table) {
            // Drop the foreign key if it exists
            $foreignKeys = Schema::getConnection()
                ->getDoctrineSchemaManager()
                ->listTableForeignKeys('persons');

            foreach ($foreignKeys as $foreignKey) {
                if (in_array('organization_id', $foreignKey->getLocalColumns())) {
                    $table->dropForeign($foreignKey->getName());
                    break;
                }
            }

            // Then remove the column
            $table->dropColumn('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('persons', function (Blueprint $table) {
            // Check if column exists before adding
            if (!Schema::hasColumn('persons', 'organization_id')) {
                // Add back the column with the correct type
                $table->unsignedInteger('organization_id')->nullable();
                
                // Add back the foreign key
                $table->foreign('organization_id')
                    ->references('id')
                    ->on('organizations')
                    ->onDelete('set null');
            }
        });

        // Restore the relationships if any
        if (Schema::hasColumn('persons', 'organization_id')) {
            foreach ($this->relationships as $relationship) {
                DB::table('persons')
                    ->where('id', $relationship->id)
                    ->update(['organization_id' => $relationship->organization_id]);
            }
        }
    }
}