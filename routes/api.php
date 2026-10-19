<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\FinanceController as F;
Route::get('/test',[F::class,'test']);
Route::get('/dashboard',[F::class,'dashboard']);
Route::get('/reports',[F::class,'reports']);
Route::get('/salaries',[F::class,'salaries']);
Route::post('/salaries',[F::class,'saveSalary']);
Route::delete('/salaries/{salary}',[F::class,'deleteSalary']);
Route::get('/expenses',[F::class,'expenses']);
Route::post('/expenses',[F::class,'addExpense']);
Route::put('/expenses/{expense}',[F::class,'updateExpense']);
Route::delete('/expenses/{expense}',[F::class,'deleteExpense']);
Route::get('/emis',[F::class,'emis']);
Route::post('/emis',[F::class,'addEmi']);
Route::put('/emis/{emi}',[F::class,'updateEmi']);
Route::delete('/emis/{emi}',[F::class,'deleteEmi']);
Route::post('/emis/{emi}/payments',[F::class,'payEmi']);
