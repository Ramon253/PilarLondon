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
        Schema::create('groups', function (Blueprint $table){
            $table->id();
            $table->file('banner');
            $table->enum('level', ['A1','A2','B1','B2','C1','C2']);
            $table->integer('capacity')->unsigned();
            $table->time('lessons_time');
            $table->enum('lesson_days', ['l-m', 'm-j', 'v']);
            $table->foreignId('teacher_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
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
