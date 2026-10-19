<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up():void{Schema::create('expenses',function(Blueprint $t){$t->id();$t->string('title');$t->string('category');$t->decimal('amount',12,2);$t->date('expense_date');$t->text('description')->nullable();$t->timestamps();$t->index('expense_date');});}public function down():void{Schema::dropIfExists('expenses');}};
