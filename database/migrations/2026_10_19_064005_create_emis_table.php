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
        Schema::create('emis', function (Blueprint $table) {
            $table->id();

            $table->string('name');
            $table->decimal('total_amount', 12, 2);
            $table->decimal('monthly_amount', 12, 2);

            $table->unsignedInteger('total_months');
            $table->date('start_date');
            $table->unsignedTinyInteger('due_day');

            $table->enum('status', ['active', 'completed'])
                ->default('active');

            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('emis');
    }
};
