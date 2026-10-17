<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('referral_code')->nullable()->after('username');
        });

        Schema::table('account_invitations', function (Blueprint $table) {
            $table->foreignId('referrer_id')->nullable()->after('invited_by')->constrained('users')->nullOnDelete();
        });

        $usedCodes = [];
        foreach (DB::table('users')->orderBy('id')->get(['id', 'name']) as $user) {
            $parts = preg_split('/\s+/', trim(preg_replace('/[^a-zA-Z0-9]+/', ' ', Str::ascii((string) $user->name)) ?: ''));
            $firstName = $parts[0] ?? 'User';
            $surnameInitial = count($parts) > 1 ? strtoupper(substr((string) end($parts), 0, 1)) : '';
            $base = substr(ucfirst(strtolower($firstName)).$surnameInitial, 0, 240) ?: 'User';
            $candidate = $base;
            $suffix = 2;

            while (isset($usedCodes[strtolower($candidate)])) {
                $candidate = $base.$suffix;
                $suffix++;
            }

            $usedCodes[strtolower($candidate)] = true;
            DB::table('users')->where('id', $user->id)->update(['referral_code' => $candidate]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unique('referral_code');
        });
    }

    public function down(): void
    {
        Schema::table('account_invitations', function (Blueprint $table) {
            $table->dropForeign(['referrer_id']);
            $table->dropColumn('referrer_id');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['referral_code']);
            $table->dropColumn('referral_code');
        });
    }
};
