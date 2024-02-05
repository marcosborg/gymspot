<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientsTable extends Migration
{
    public function up()
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('address')->nullable();
            $table->string('zip')->nullable();
            $table->string('location')->nullable();
            $table->string('phone')->nullable();
            $table->string('vat')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
