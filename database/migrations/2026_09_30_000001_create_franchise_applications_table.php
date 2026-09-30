<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('franchise_applications', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('full_name');
            $table->string('email');
            $table->string('phone_number', 40);
            $table->string('preferred_package', 2);
            $table->string('location');
            $table->text('business_background')->nullable();
            $table->string('investment_capacity', 100)->nullable();
            $table->text('additional_notes')->nullable();
            $table->timestamps();

            $table->index('email');
            $table->index('preferred_package');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('franchise_applications');
    }
};
