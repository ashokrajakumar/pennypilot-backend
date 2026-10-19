<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class EmiPayment extends Model {protected $fillable=['emi_id','amount','due_date','paid_date','status'];protected function casts():array{return ['amount'=>'decimal:2','due_date'=>'date:Y-m-d','paid_date'=>'date:Y-m-d'];}}
