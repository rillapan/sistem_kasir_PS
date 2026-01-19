<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

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
    
    Route::get('/chart-pie-data', [App\Http\Controllers\HomeController::class, 'pieCartData2']);
    Route::get('/chart-area-data', 'App\Http\Controllers\HomeController@areaCartData');
    Route::get('/hourly-revenue-data', 'App\Http\Controllers\HomeController@hourlyRevenueData');
    
    Route::get('/profile', 'App\Http\Controllers\HomeController@profile')->name('profile');
    Route::post('/device/{id}/update-status', [App\Http\Controllers\DeviceController::class, 'updateStatus'])->name('device.updateStatus');
    Route::post('/device/update-all-statuses', [App\Http\Controllers\DeviceController::class, 'updateDeviceStatusesFromTimers'])->name('device.updateAllStatuses');
    Route::get('/device/{id}/test-status', [App\Http\Controllers\DeviceController::class, 'testStatus'])->name('device.testStatus');
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
    // Debug endpoint for device testing
    Route::get('/debug/devices', [App\Http\Controllers\DebugController::class, 'deviceDebug']);
    
    // Transaction
    Route::resource('/transaction', App\Http\Controllers\TransactionController::class);
    Route::get('/transaction/{id}/payment', [App\Http\Controllers\TransactionController::class, 'showPayment'])->name('transaction.showPayment');
    Route::post('/transaction/{id}/process-payment', [App\Http\Controllers\TransactionController::class, 'processPayment'])->name('transaction.processPayment');
    Route::get('/transaction/{id}/print', [App\Http\Controllers\TransactionController::class, 'printReceipt'])->name('transaction.print');
    Route::put('/transaction/{id}/update', 'App\Http\Controllers\TransactionController@updateStatus');
    Route::post('/transaction/{id}/end', 'App\Http\Controllers\TransactionController@endTransaction')->name('transaction.end');
    Route::get('/transaction/{id}/add-order', [App\Http\Controllers\TransactionController::class, 'addOrder'])->name('transaction.add-order');
    Route::post('/transaction/{id}/store-order', [App\Http\Controllers\TransactionController::class, 'storeOrder'])->name('transaction.store-order');
    
    // FnB Management in Transaction Edit
    Route::post('/transaction/{id}/fnb/add', [App\Http\Controllers\TransactionController::class, 'addFnbToTransaction'])->name('transaction.fnb.add');
    Route::put('/transaction/{id}/fnb/{fnbId}/update', [App\Http\Controllers\TransactionController::class, 'updateTransactionFnb'])->name('transaction.fnb.update');
    Route::delete('/transaction/{id}/fnb/{fnbId}/delete', [App\Http\Controllers\TransactionController::class, 'deleteTransactionFnb'])->name('transaction.fnb.delete');


    // Device
    Route::resource('/device', App\Http\Controllers\DeviceController::class);
    Route::get('/device/by-playstation/{id}', [App\Http\Controllers\DeviceController::class, 'byPlaystation'])->name('device.byPlaystation');
    Route::get('/booking/{id}/add', [App\Http\Controllers\DeviceController::class, 'bookingAdd']);
    Route::get('/booking/{id}', [App\Http\Controllers\DeviceController::class, 'booking']);
    Route::post('/device/{id}/stop', [App\Http\Controllers\DeviceController::class, 'stopDevice'])->name('device.stop');
    Route::get('/device/{id}/debug', [App\Http\Controllers\DeviceController::class, 'debugDevice'])->name('device.debug');
    Route::get('/device/{id}/test-stop', [App\Http\Controllers\DeviceController::class, 'testStopDevice'])->name('device.testStop');

    // Expense (Kasir creates, Admin manages)
    Route::resource('/expense', App\Http\Controllers\ExpenseController::class);
});

// Admin + Owner Routes
Route::middleware(['auth', 'check_role:admin,owner'])->group(function () {
    // Reports
    Route::get('/report', 'App\Http\Controllers\ReportController@index')->name('report');
    Route::get('/report/transaction', 'App\Http\Controllers\ReportController@transactionReport')->name('transaction.report');
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
    Route::post('/users/{id}/reset-password', [App\Http\Controllers\UserManagementController::class, 'resetPassword'])->name('users.reset-password');

    // Master Data
    Route::resource('/playstation', App\Http\Controllers\PlayController::class);
    Route::resource('/fnb', App\Http\Controllers\FnbController::class);
    Route::resource('/price-group', App\Http\Controllers\PriceGroupController::class);
    Route::get('/price-group/{id}/fnbs', [App\Http\Controllers\PriceGroupController::class, 'showFnbs'])->name('price-group.fnbs');

    // Hourly Prices for PlayStation
    Route::prefix('/playstation/{playstationId}')->name('hourly-prices.')->group(function () {
        Route::resource('/hourly-prices', 'App\Http\Controllers\HourlyPriceController');
    });
    
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
    
    // App Settings (Admin only)
    Route::get('/settings', [App\Http\Controllers\AppSettingsController::class, 'index'])->name('settings.index');
    Route::post('/settings/reset-transactions', [App\Http\Controllers\AppSettingsController::class, 'resetTransactions'])->name('settings.reset-transactions');
    
    // Work Shift Management
    Route::resource('/work_shifts', App\Http\Controllers\WorkShiftController::class);
    
});
