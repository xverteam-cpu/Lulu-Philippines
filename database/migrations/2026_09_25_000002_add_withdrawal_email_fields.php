<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->string('transaction_reference', 50)->nullable()->unique()->after('amount');
            $table->decimal('processing_fee', 12, 2)->nullable()->after('transaction_reference');
            $table->decimal('total_withdrawn', 12, 2)->nullable()->after('processing_fee');
        });
    }

    public function down(): void
    {
        Schema::table('withdrawals', function (Blueprint $table): void {
            $table->dropUnique(['transaction_reference']);
            $table->dropColumn(['transaction_reference', 'processing_fee', 'total_withdrawn']);
        });
    }
};
