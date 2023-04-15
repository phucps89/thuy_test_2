<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_registrations', function (Blueprint $table) {
            $table->unsignedBigInteger('id_course');
            $table->unsignedBigInteger('id_user');

            $table->foreign('id_user')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('id_course')
                ->on('courses')
                ->references('id')
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_registrations');
    }
};
