<?php
use Illuminate\Support\Facades\Route;
Route::get('/',fn()=>response()->json(['app'=>'PennyPilot API','status'=>'ok']));
