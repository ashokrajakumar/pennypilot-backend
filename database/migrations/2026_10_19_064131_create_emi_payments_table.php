<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */

    public function up(): void
    {
        Schema::create('emi_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('emi_id')
                ->constrained('emis')
                ->cascadeOnDelete();

            $table->decimal('amount', 12, 2);

            $table->date('due_date');

            $table->date('paid_date')->nullable();

            $table->enum('status', [
                'pending',
                'paid'
            ])->default('pending');

            $table->timestamps();

            $table->unique(['emi_id', 'due_date']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emi_payments');
    }
};
