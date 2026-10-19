<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {public function up():void{Schema::create('salaries',function(Blueprint $t){$t->id();$t->decimal('amount',12,2);$t->unsignedTinyInteger('month');$t->unsignedSmallInteger('year');$t->timestamps();$t->unique(['month','year']);});}public function down():void{Schema::dropIfExists('salaries');}};
