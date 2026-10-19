<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Salary extends Model {protected $fillable=['amount','month','year'];protected function casts():array{return ['amount'=>'decimal:2','month'=>'integer','year'=>'integer'];}}
