<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table): void {
            $table->text('agreement_signature_data')->nullable()->after('agreement_signature_name');
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table): void {
            $table->dropColumn('agreement_signature_data');
        });
    }
};
