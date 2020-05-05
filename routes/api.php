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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


Route::post('login', 'AuthController@login')->name('login');
Route::post('refresh', 'AuthController@refresh');
Route::group(['middleware' => 'auth.jwt'], function(){
    Route::post('me', 'AuthController@me');
    Route::post('logout', 'AuthController@logout');
    Route::get('/', 'HomeController@index')->name('home');
    Route::get('branches/get', 'BranchController@get');
    Route::get('warehouses/get', 'WarehouseController@get');
    Route::get('storages/get', 'StorageController@get');
    Route::get('companies/get', 'CompanyController@get');
    Route::get('products/get', 'ProductController@get');
    Route::get('transactions/get', 'TransactionController@get');
    Route::get('getProductTransactions', 'TransactionController@getProductTransactions');
    Route::get('inventories/checkCurrentQty', 'InventoryController@checkCurrentQty');
    Route::resource('branches', 'BranchController')->only(['index','store','edit','update']);
    Route::resource('warehouses', 'WarehouseController')->only(['index','store','edit','update']);
    Route::resource('storages', 'StorageController')->only(['index','store','edit','update']);
    Route::resource('companies', 'CompanyController')->only(['index','store','edit','update']);
    Route::resource('products', 'ProductController')->only(['index','store','edit','update']);
    Route::resource('inventories', 'InventoryController')->only(['index','store','edit','update']);
    Route::resource('transactions', 'TransactionController')->only(['index','store','edit','update']);
    Route::resource('users', 'UserController')->only(['index','store','edit','update']);
    Route::put('user/allow', 'UserController@allow');
});
