<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up():void{Schema::create('emi_payments',function(Blueprint $t){$t->id();$t->foreignId('emi_id')->constrained('emis')->cascadeOnDelete();$t->decimal('amount',12,2);$t->date('due_date');$t->date('paid_date')->nullable();$t->enum('status',['pending','paid'])->default('pending');$t->timestamps();$t->unique(['emi_id','due_date']);});}public function down():void{Schema::dropIfExists('emi_payments');}};
