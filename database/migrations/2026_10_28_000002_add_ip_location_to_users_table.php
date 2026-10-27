<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ip_location')->nullable()->after('last_ip_address');
            $table->timestamp('ip_location_updated_at')->nullable()->after('ip_location');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['ip_location', 'ip_location_updated_at']);
        });
    }
};
