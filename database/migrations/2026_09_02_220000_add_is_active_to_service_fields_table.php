<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_fields', function (Blueprint $table): void {
            $table->boolean('is_active')->default(true)->after('is_required');
            $table->index(['service_id', 'is_active', 'sort_order']);
        });
    }

    public function down(): void
    {
        Schema::table('service_fields', function (Blueprint $table): void {
            $table->dropIndex(['service_fields_service_id_is_active_sort_order_index']);
            $table->dropColumn('is_active');
        });
    }
};
