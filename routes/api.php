<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', 'AuthController@login')->name('login');
Route::post('refresh', 'AuthController@refresh');

Route::group(['middleware' => 'auth:api'], function() {
    Route::post('me', 'AuthController@me');
    Route::post('logout', 'AuthController@logout');
    
    Route::resource('users', 'UserController')->only(['index','store','edit','update']);
    Route::put('user/allow', 'UserController@allow');
    
    Route::get('/', 'HomeController@index')->name('home');

    Route::get('transactions/get', 'TransactionController@get');
    Route::get('getProductTransactions', 'TransactionController@getProductTransactions');
    Route::resource('transactions', 'TransactionController')->only(['index','store','edit','update']);
    
    Route::get('branches/get', 'BranchController@get');
    Route::resource('branches', 'BranchController')->only(['index','store','edit','update']);

    Route::get('warehouses/get', 'WarehouseController@get');
    Route::resource('warehouses', 'WarehouseController')->only(['index','store','edit','update']);

    Route::get('storages/get', 'StorageController@get');
    Route::resource('storages', 'StorageController')->only(['index','store','edit','update']);
    
    Route::get('companies/get', 'CompanyController@get');
    Route::resource('companies', 'CompanyController')->only(['index','store','edit','update']);

    Route::get('products/get', 'ProductController@get');
    Route::resource('products', 'ProductController')->only(['index','store','edit','update']);

    Route::get('inventories/checkCurrentQty', 'InventoryController@checkCurrentQty');
    Route::get('inventories/getCriticalLevelProducts', 'InventoryController@getCriticalLevelProducts');
    Route::resource('inventories', 'InventoryController')->only(['index','store','edit','update']);
});
