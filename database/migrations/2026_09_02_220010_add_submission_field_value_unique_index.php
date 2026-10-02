<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_field_values', function (Blueprint $table): void {
            $table->unique(['submission_id', 'service_field_id'], 'submission_field_values_submission_field_unique');
        });
    }

    public function down(): void
    {
        Schema::table('submission_field_values', function (Blueprint $table): void {
            $table->dropUnique('submission_field_values_submission_field_unique');
        });
    }
};
