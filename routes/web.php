<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect('/home');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home')->middleware('auth');

Route::resource('/playstation', App\Http\Controllers\PlayController::class)->middleware('auth');
Route::resource('/transaction', App\Http\Controllers\TransactionController::class)->middleware('auth');

Route::get('/transaction/{id}/payment', [App\Http\Controllers\TransactionController::class, 'showPayment'])->name('transaction.showPayment')->middleware('auth');
Route::post('/transaction/{id}/process-payment', [App\Http\Controllers\TransactionController::class, 'processPayment'])->name('transaction.processPayment')->middleware('auth');
Route::resource('/device', App\Http\Controllers\DeviceController::class)->middleware('auth');
Route::get('/device/by-playstation/{id}', [App\Http\Controllers\DeviceController::class, 'byPlaystation'])->name('device.byPlaystation')->middleware('auth');

Route::put('/transaction/{id}/update', 'App\Http\Controllers\TransactionController@updateStatus')->middleware('auth');
Route::post('/transaction/{id}/end', 'App\Http\Controllers\TransactionController@endTransaction')->middleware('auth')->name('transaction.end');
Route::post('/device/{id}/update-status', 'App\Http\Controllers\DeviceController@updateStatusAjax')->middleware('auth');
Route::get('/report', 'App\Http\Controllers\ReportController@index')->middleware('auth')->name('report');
Route::get('/generate-pdf', 'App\Http\Controllers\ReportController@generatePDF')->middleware('auth');
Route::get('/generate-excel', 'App\Http\Controllers\ReportController@generateExcel')->middleware('auth')->name('laporan.excel');


Route::get('/booking/{id}/add', 'App\Http\Controllers\DeviceController@bookingAdd')->middleware('auth');
Route::get('/booking/{id}', 'App\Http\Controllers\DeviceController@booking')->middleware('auth');

Route::get('/chart-pie-data', 'App\Http\Controllers\HomeController@pieCartData2');
Route::get('/profile', 'App\Http\Controllers\HomeController@profile')->middleware('auth')->name('profile');
Route::put('/profile/{id}', 'App\Http\Controllers\HomeController@update')->middleware('auth');

Route::get('/chart-area-data', 'App\Http\Controllers\HomeController@areaCartData');

Route::get('/fnb/laporan-penjualan', [App\Http\Controllers\FnbController::class, 'laporanPenjualan'])->name('fnb.laporan');
Route::get('/fnb/laporan-penjualan/excel', [App\Http\Controllers\FnbController::class, 'exportExcel'])->name('fnb.laporan.excel');
Route::get('/fnb/laporan-penjualan/pdf', [App\Http\Controllers\FnbController::class, 'exportPdf'])->name('fnb.laporan.pdf');

// Panduan Route
Route::get('/panduan', [App\Http\Controllers\PanduanController::class, 'index'])->name('panduan.index');
Route::resource('/fnb', App\Http\Controllers\FnbController::class)->middleware('auth');

Route::get('/stock', [App\Http\Controllers\StockController::class, 'index'])->middleware('auth')->name('stock.index');
Route::get('/stock/{id}/add', [App\Http\Controllers\StockController::class, 'addForm'])->middleware('auth')->name('stock.add');
Route::post('/stock/{id}/add', [App\Http\Controllers\StockController::class, 'addStock'])->middleware('auth');
Route::get('/stock/{id}/reduce', [App\Http\Controllers\StockController::class, 'reduceForm'])->middleware('auth')->name('stock.reduce');
Route::post('/stock/{id}/reduce', [App\Http\Controllers\StockController::class, 'reduceStock'])->middleware('auth');
Route::get('/stock/{id}/history', [App\Http\Controllers\StockController::class, 'history'])->middleware('auth')->name('stock.history');
