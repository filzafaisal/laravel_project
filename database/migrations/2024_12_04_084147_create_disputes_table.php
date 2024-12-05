<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDisputesTable extends Migration
{
    public function up()
    {
        Schema::create('disputes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vendor_id'); // Foreign key to Vendor table
            $table->string('subject'); // Subject of the dispute
            $table->text('description'); // Description of the dispute
            $table->enum('status', ['in-review', 'resolved', 'pending'])->default('pending'); // Status of the dispute
            $table->json('files')->nullable(); // To store file paths
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('disputes');
    }
}
