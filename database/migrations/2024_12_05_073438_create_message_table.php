<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageTable extends Migration
{
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispute_id'); // Foreign key to Dispute table
            $table->text('message')->nullable(); // Content of the message
            $table->json('files')->nullable(); // Content of the message
            $table->timestamps();

            // Foreign key constraint
            $table->foreign('dispute_id')->references('id')->on('disputes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('messages');
    }
}

