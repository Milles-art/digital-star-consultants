<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submission_field_values', function (Blueprint $table): void {
            $table->string('field_label_snapshot')->nullable()->after('service_field_id');
            $table->string('field_key_snapshot')->nullable()->after('field_label_snapshot');
            $table->string('field_type_snapshot')->nullable()->after('field_key_snapshot');
            $table->json('field_options_snapshot')->nullable()->after('field_type_snapshot');
        });
    }

    public function down(): void
    {
        Schema::table('submission_field_values', function (Blueprint $table): void {
            $table->dropColumn([
                'field_label_snapshot',
                'field_key_snapshot',
                'field_type_snapshot',
                'field_options_snapshot',
            ]);
        });
    }
};
