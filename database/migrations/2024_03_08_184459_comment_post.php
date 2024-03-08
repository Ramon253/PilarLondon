<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('post_comments', function (Blueprint $table){
            $table->id();
            $table->text('content');
            $table->boolean('public');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('post_id');

            $table->foreign('user_id')->references('id')->on('students')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('parent_id')->references('id')->on('post_comments')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('post_id')->references('id')->on('posts')->cascadeOnUpdate()->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
