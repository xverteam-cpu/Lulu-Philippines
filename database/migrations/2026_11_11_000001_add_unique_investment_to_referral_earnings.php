<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('referral_earnings', function (Blueprint $table) {
            $table->unique('investment_id');
        });
    }

    public function down(): void
    {
        Schema::table('referral_earnings', function (Blueprint $table) {
            $table->dropUnique(['investment_id']);
        });
    }
};
