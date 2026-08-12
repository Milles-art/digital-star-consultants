<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->string('label');
            $table->string('field_key');
            // text, textarea, date, select, email, tel, number, file
            $table->string('field_type')->default('text');
            $table->json('options')->nullable();
            $table->string('placeholder')->nullable();
            $table->text('help_text')->nullable();
            $table->boolean('is_required')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique(['service_id', 'field_key']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_fields');
    }
};
