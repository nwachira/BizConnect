<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TransactionController;


Route::get('/', function () {
    return view('welcome');
});
Route::get('/{record}/pdf/download',[App\Http\Controllers\TransactionController::class,'transaction'])->name('transaction.pdf.download');