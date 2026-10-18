<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->string('webauthn_user_handle', 64)->nullable()->unique();
        });

        Schema::create('passkeys', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name', 80);
            $table->text('credential_id');
            $table->char('credential_id_hash', 64)->unique();
            $table->longText('credential_source');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('passkeys');

        Schema::table('users', function (Blueprint $table): void {
            $table->dropUnique(['webauthn_user_handle']);
            $table->dropColumn('webauthn_user_handle');
        });
    }
};
