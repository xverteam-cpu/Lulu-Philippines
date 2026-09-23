<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('investments', function (Blueprint $table): void {
            $table->string('agreement_version')->nullable()->after('last_interest_accrued_at');
            $table->string('agreement_signature_name')->nullable()->after('agreement_version');
            $table->timestamp('agreement_signed_at')->nullable()->after('agreement_signature_name');
        });
    }

    public function down(): void
    {
        Schema::table('investments', function (Blueprint $table): void {
            $table->dropColumn([
                'agreement_version',
                'agreement_signature_name',
                'agreement_signed_at',
            ]);
        });
    }
};
