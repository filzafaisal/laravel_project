<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVendorsTable extends Migration
{
    public function up()
    {
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_id');
            $table->string('name_of_vendor');
            $table->string('category_of_products');
            $table->string('email')->unique();
            $table->string('registered_office_address');
            $table->string('head_office_address');
            $table->string('sector');
            $table->string('website')->nullable();
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('vendors');
    }
}
