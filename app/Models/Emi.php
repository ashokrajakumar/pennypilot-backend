<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Emi extends Model {
 protected $fillable=['name','total_amount','monthly_amount','total_months','start_date','due_day','status'];
 protected function casts():array{return ['total_amount'=>'decimal:2','monthly_amount'=>'decimal:2','total_months'=>'integer','start_date'=>'date:Y-m-d','due_day'=>'integer'];}
 public function payments():HasMany{return $this->hasMany(EmiPayment::class);}
}
