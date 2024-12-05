<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveWebsiteAndSectorFromVendorsTable extends Migration
{
    public function up()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->dropColumn(['website', 'sector']);
        });
    }

    public function down()
    {
        Schema::table('vendors', function (Blueprint $table) {
            $table->string('website')->nullable();
            $table->string('sector');
        });
    }
}
