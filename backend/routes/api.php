<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InvoiceController;

// Invoice API routes will be added here 

Route::apiResource('invoices', InvoiceController::class); 
Route::post('invoices/{id}/pay', [InvoiceController::class, 'markAsPaid']); 