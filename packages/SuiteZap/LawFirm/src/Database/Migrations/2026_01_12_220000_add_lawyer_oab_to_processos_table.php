<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->string('advogado_responsavel_nome')->nullable()->after('status');
            $table->string('advogado_responsavel_oab')->nullable()->after('advogado_responsavel_nome');
        });
    }

    public function down()
    {
        Schema::table('processos', function (Blueprint $table) {
            $table->dropColumn(['advogado_responsavel_oab', 'advogado_responsavel_nome']);
        });
    }
};
