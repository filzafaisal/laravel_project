<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWebsiteAndSectorToOwnersTable extends Migration
{
    public function up()
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->string('website')->nullable(); // Website field
            $table->string('sector')->nullable();  // Sector field
        });
    }

    public function down()
    {
        Schema::table('owners', function (Blueprint $table) {
            $table->dropColumn(['website', 'sector']);
        });
    }
}
