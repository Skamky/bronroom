<?php

use Database\Seeders\RoleSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->unique()->primary();
            $table->string('code')->unique();
            $table->string('label')->nullable();
            $table->timestamps();
        });
        Artisan::call('db:seed', ['--class' => RoleSeeder::class]);

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->after('email')->default(0)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['role_id']);
        $table->dropColumn('role_id');
    });
        Schema::dropIfExists('roles');
    }
};
