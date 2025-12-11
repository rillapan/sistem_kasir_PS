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

// Universal Authenticated Routes
Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    Route::get('/chart-pie-data', 'App\Http\Controllers\HomeController@pieCartData2');
    Route::get('/chart-area-data', 'App\Http\Controllers\HomeController@areaCartData');
    Route::get('/hourly-revenue-data', 'App\Http\Controllers\HomeController@hourlyRevenueData');
    
    Route::get('/profile', 'App\Http\Controllers\HomeController@profile')->name('profile');
    Route::put('/profile/{id}', 'App\Http\Controllers\HomeController@update');
    
    Route::get('/server-time', function() {
        return response()->json([
            'server_time' => now()->toISOString(),
            'server_timestamp' => now()->timestamp * 1000
        ]);
    });
});

// Admin + Kasir Routes
Route::middleware(['auth', 'check_role:admin,kasir'])->group(function () {
    // Transaction
    Route::resource('/transaction', App\Http\Controllers\TransactionController::class);
    Route::get('/transaction/{id}/payment', [App\Http\Controllers\TransactionController::class, 'showPayment'])->name('transaction.showPayment');
    Route::post('/transaction/{id}/process-payment', [App\Http\Controllers\TransactionController::class, 'processPayment'])->name('transaction.processPayment');
    Route::get('/transaction/{id}/print', [App\Http\Controllers\TransactionController::class, 'printReceipt'])->name('transaction.print');
    Route::put('/transaction/{id}/update', 'App\Http\Controllers\TransactionController@updateStatus');
    Route::post('/transaction/{id}/end', 'App\Http\Controllers\TransactionController@endTransaction')->name('transaction.end');
    Route::get('/transaction/{id}/add-order', [App\Http\Controllers\TransactionController::class, 'addOrder'])->name('transaction.add-order');
    Route::post('/transaction/{id}/store-order', [App\Http\Controllers\TransactionController::class, 'storeOrder'])->name('transaction.store-order');

    // Device
    Route::resource('/device', App\Http\Controllers\DeviceController::class);
    Route::get('/device/by-playstation/{id}', [App\Http\Controllers\DeviceController::class, 'byPlaystation'])->name('device.byPlaystation');
    Route::post('/device/{id}/update-status', 'App\Http\Controllers\DeviceController@updateStatusAjax');
    Route::get('/booking/{id}/add', 'App\Http\Controllers\DeviceController@bookingAdd');
    Route::get('/booking/{id}', 'App\Http\Controllers\DeviceController@booking');

    // Expense (Kasir creates, Admin manages)
    Route::resource('/expense', App\Http\Controllers\ExpenseController::class);
});

// Admin + Owner Routes
Route::middleware(['auth', 'check_role:admin,owner'])->group(function () {
    // Reports
    Route::get('/report', 'App\Http\Controllers\ReportController@index')->name('report');
    Route::get('/generate-pdf', 'App\Http\Controllers\ReportController@generatePDF');
    Route::get('/generate-excel', 'App\Http\Controllers\ReportController@generateExcel')->name('laporan.excel');

    // FnB Reports
    Route::get('/fnb/laporan-penjualan', [App\Http\Controllers\FnbController::class, 'laporanPenjualan'])->name('fnb.laporan');
    Route::get('/fnb/laporan-penjualan/excel', [App\Http\Controllers\FnbController::class, 'exportExcel'])->name('fnb.laporan.excel');
    Route::get('/fnb/laporan-penjualan/pdf', [App\Http\Controllers\FnbController::class, 'exportPdf'])->name('fnb.laporan.pdf');
});

// Admin Only Routes
Route::middleware(['auth', 'check_role:admin'])->group(function () {
    // User Management
    Route::resource('/users', App\Http\Controllers\UserManagementController::class);

    // Master Data
    Route::resource('/playstation', App\Http\Controllers\PlayController::class);
    Route::resource('/fnb', App\Http\Controllers\FnbController::class);
    Route::resource('/price-group', App\Http\Controllers\PriceGroupController::class);
    Route::get('/price-group/{id}/fnbs', [App\Http\Controllers\PriceGroupController::class, 'showFnbs'])->name('price-group.fnbs');
    
    // Stock
    Route::get('/stock', [App\Http\Controllers\StockController::class, 'index'])->name('stock.index');
    Route::get('/stock/{id}/add', [App\Http\Controllers\StockController::class, 'addForm'])->name('stock.add');
    Route::post('/stock/{id}/add', [App\Http\Controllers\StockController::class, 'addStock']);
    Route::get('/stock/{id}/reduce', [App\Http\Controllers\StockController::class, 'reduceForm'])->name('stock.reduce');
    Route::post('/stock/{id}/reduce', [App\Http\Controllers\StockController::class, 'reduceStock']);
    Route::get('/stock/{id}/history', [App\Http\Controllers\StockController::class, 'history'])->name('stock.history');

    // Settings / Configs
    Route::resource('/expense-category', App\Http\Controllers\ExpenseCategoryController::class);
    Route::resource('/custom-package', App\Http\Controllers\CustomPackageController::class);
    Route::put('/custom-package/{id}/toggle-status', [App\Http\Controllers\CustomPackageController::class, 'toggleStatus'])->name('custom-package.toggle-status');
    
    // Guide
    Route::get('/panduan', [App\Http\Controllers\PanduanController::class, 'index'])->name('panduan.index');
});
