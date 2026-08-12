<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number')->unique();
            $table->foreignId('service_id')->constrained();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();
            // pending, in_progress, completed, rejected, awaiting_customer
            $table->string('status')->default('pending');
            
            // Added missing fields
            $table->timestamp('preferred_date')->nullable();
            $table->text('customer_notes')->nullable();
            $table->decimal('total_price', 10, 2)->nullable();
            
            $table->text('staff_notes')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('preferred_date');
            $table->index('customer_email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submissions');
    }
};