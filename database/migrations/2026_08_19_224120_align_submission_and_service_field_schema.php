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
        Schema::table('submissions', function (Blueprint $table): void {
            $table->string('payment_status')->default('pending')->after('status');
            $table->string('payment_method')->nullable()->after('payment_status');
        });

        Schema::table('service_fields', function (Blueprint $table): void {
            $table->string('default_value')->nullable()->after('help_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_fields', function (Blueprint $table): void {
            $table->dropColumn('default_value');
        });

        Schema::table('submissions', function (Blueprint $table): void {
            $table->dropColumn(['payment_status', 'payment_method']);
        });
    }
};
