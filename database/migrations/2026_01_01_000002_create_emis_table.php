<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up():void{Schema::create('emis',function(Blueprint $t){$t->id();$t->string('name');$t->decimal('total_amount',12,2);$t->decimal('monthly_amount',12,2);$t->unsignedInteger('total_months');$t->date('start_date');$t->unsignedTinyInteger('due_day');$t->enum('status',['active','completed'])->default('active');$t->timestamps();});}public function down():void{Schema::dropIfExists('emis');}};
