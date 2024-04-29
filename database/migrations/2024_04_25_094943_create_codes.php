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
        Schema::create('join_codes', function (Blueprint $table) {

            $table->string('code')->uniqueid();
            $table->enum('role', ['student', 'teacher', 'parent'])->default('student');
            $table->foreignId('user_id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate()
                ->unique()
                ->nullable();

            $table->timestamps();

            $table->primary('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('codes');
    }
};
