<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Add a username so technical staff can log in with a username
     * instead of having to remember an email address. Existing users
     * get a username backfilled from their email address.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username', 30)->nullable()->unique()->after('name');
        });

        foreach (DB::table('users')->whereNull('username')->get() as $user) {
            $base = strtolower((string) preg_replace('/[^a-z0-9]+/', '_', Str::before($user->email, '@')));
            $base = trim($base, '_');
            if ($base === '') {
                $base = 'user';
            }
            $username = $base;
            if (DB::table('users')->where('username', $username)->exists()) {
                $username = $base.'_'.$user->id;
            }
            DB::table('users')->where('id', $user->id)->update(['username' => $username]);
        }
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('username');
        });
    }
};
