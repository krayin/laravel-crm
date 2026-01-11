<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('law_financials', function (Blueprint $table) {
            // Data de emissão/faturamento
            $table->date('issued_at')->nullable()->after('data_vencimento');

            // Data real do recebimento (pago_em)
            $table->date('payment_date')->nullable()->after('issued_at');

            // Categoria: honorario, custas, reembolso, sucumbencia
            $table->string('category')->nullable()->after('payment_date');

            // Método de pagamento: boleto, pix, cartao
            $table->payment_method = $table->string('payment_method')->nullable()->after('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('law_financials', function (Blueprint $table) {
            $table->dropColumn(['issued_at', 'payment_date', 'category', 'payment_method']);
        });
    }
};
