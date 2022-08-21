<?php

use App\Http\Controllers\Apps\CategoryController;
use App\Http\Controllers\Apps\CustomerController;
use App\Http\Controllers\Apps\DashboardController;
use App\Http\Controllers\Apps\PermissionController;
use App\Http\Controllers\Apps\ProductController;
use App\Http\Controllers\Apps\ProfitController;
use App\Http\Controllers\Apps\RoleController;
use App\Http\Controllers\Apps\SaleController;
use App\Http\Controllers\Apps\TransactionController;
use App\Http\Controllers\Apps\UserController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

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
    return Inertia::render('Auth/Login');
})->middleware('guest');

Route::prefix('apps')->group(function(){
    Route::group(['middleware' => ['auth']], function(){
        Route::get('dashboard',DashboardController::class)->name('apps.dashboard');
        Route::get('/permissions',PermissionController::class)->name('apps.permissions.index')
               ->middleware('permission:permissions.index');
        Route::resource('/roles', RoleController::class, ['as' => 'apps'])
               ->middleware('permission:roles.index|roles.create|roles.edit|roles.delete');
        Route::resource('users', UserController::class, ['as' => 'apps'])
               ->middleware('permission:users.index|users.create|users.edit|users.delete');
        Route::resource('/categories', CategoryController::class, ['as' => 'apps'])
               ->middleware('permission:categories.index|categories.create|categories.edit|categories.delete');
        Route::resource('/products', ProductController::class, ['as' => 'apps'])
               ->middleware('permission:products.index|products.create|products.edit|products.delete');
        Route::resource('/customers', CustomerController::class, ['as' => 'apps'])
               ->middleware('permission:customers.index|customers.create|customers.edit|customers.delete');

        // Transacion
        Route::get('/transactions', [TransactionController::class, 'index'])->name('apps.transactions.index');
        Route::post('/transactions/searchProduct', [TransactionController::class, 'searchProduct'])->name('apps.transactions.searchProduct');
        Route::post('/transactions/addToCart', [TransactionController::class, 'addToCart'])->name('apps.transactions.addToCart');
        Route::post('/transactions/destroyCart', [TransactionController::class, 'destroyCart'])->name('apps.transactions.destroyCart');
        Route::post('/transactions/store', [TransactionController::class, 'store'])->name('apps.transactions.store');
        Route::get('/transactions/print', [TransactionController::class, 'print'])->name('apps.transactions.print');

        // Sale
        Route::get('/sales', [SaleController::class, 'index'])->middleware('permission:sales.index')->name('apps.sales.index');
        Route::get('/sales/filter', [SaleController::class, 'filter'])->name('apps.sales.filter');
        Route::get('/sales/export',[SaleController::class, 'export'])->name('apps.sales.export');
        Route::get('/sales/pdf', [SaleController::class, 'pdf'])->name('apps.sales.pdf');

        // Profit
        Route::get('/profits', [ProfitController::class, 'index'])->middleware('permission:profits.index')->name('apps.profits.index');
        Route::get('/profits/filter', [ProfitController::class, 'filter'])->name('apps.profits.filter');
        Route::get('/profits/export', [ProfitController::class, 'export'])->name('apps.profits.export');
        Route::get('/profits/pdf', [ProfitController::class, 'pdf'])->name('apps.profits.pdf');
    });
});
