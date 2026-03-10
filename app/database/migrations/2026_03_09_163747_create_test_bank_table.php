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
        Schema::create('test_bank', function (Blueprint $table) {
            $table->id();

            $table->string('num');          // 1,2,18a
            $table->string('subject');
            $table->string('grade');
            $table->string('lang');
            $table->string('variant');

            $table->string('type');         // Single, Multiple, Matching, Short Answer, Context

            $table->text('context')->nullable();
            $table->text('question')->nullable();

            $table->longText('options')->nullable();
            $table->text('correct_answer')->nullable();

            $table->integer('points');

            $table->string('img')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('test_bank');
    }
};
