<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('provider')
                ->nullable()
                ->after('remember_token');

            $table->string('provider_id')
                ->nullable()
                ->after('provider');

            $table->string('provider_token')
                ->nullable()
                ->after('provider_id');

            $table->string('provider_refresh_token')
                ->nullable()
                ->after('provider_token');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'provider',
                'provider_id',
                'provider_token',
                'provider_refresh_token'
            ]);
        });
    }
};
