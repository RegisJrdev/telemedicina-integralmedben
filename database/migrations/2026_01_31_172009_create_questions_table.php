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
        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['text', 'email', 'number', 'tel', 'date', 'option']);
            $table->json('options')->nullable();
            $table->boolean('is_required')->default(false);
            $table->boolean('is_unique')->default(false);
            $table->boolean('is_active')->default(true);

            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tenant_questions', function (Blueprint $table) {
            $table->id();
            $table->string('tenant_id');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')
                ->onDelete('cascade');
        });

        Schema::create('central_patients', function (Blueprint $table) {
            $table->id();
            $table->string('cpf')->unique();
            $table->string('tenant_id');
            $table->timestamps();

            $table->foreign('tenant_id')->references('id')->on('tenants')
                ->onDelete('cascade');
        });

        Schema::create('central_patient_answers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('central_patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->string('answer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('central_patient_answers');
        Schema::dropIfExists('central_patients');
        Schema::dropIfExists('tenant_questions');
        Schema::dropIfExists('questions');
    }
};
