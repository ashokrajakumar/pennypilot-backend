<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Expense extends Model {protected $fillable=['title','category','amount','expense_date','description'];protected function casts():array{return ['amount'=>'decimal:2','expense_date'=>'date:Y-m-d'];}}
