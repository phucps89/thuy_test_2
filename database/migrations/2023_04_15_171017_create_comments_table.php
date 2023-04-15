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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_parent')->nullable();
            $table->unsignedBigInteger('id_post');
            $table->unsignedBigInteger('id_user');
            $table->text('comment');
            $table->timestamps();

            $table->foreign('id_user')
                ->on('users')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('id_post')
                ->on('posts')
                ->references('id')
                ->cascadeOnDelete();

            $table->foreign('id_parent')
                ->on('comments')
                ->references('id')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('comments');
    }
};
