<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('account_activities', function (Blueprint $table): void {
            $table->uuid('transfer_reference')->nullable()->index();
        });
    }

    public function down(): void
    {
        Schema::table('account_activities', function (Blueprint $table): void {
            $table->dropIndex(['transfer_reference']);
            $table->dropColumn('transfer_reference');
        });
    }
};
