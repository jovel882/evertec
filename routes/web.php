<?php

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
    return view('home');
})->name('home');

Auth::routes([
    'reset' => false,
    'confirm' => false,
    'verify' => false,
]);

Route::group(
    [
        'prefix' => 'orders', 
        'middleware' => ['auth'],
    ],
    function() {
        Route::get('/','OrderController@index')
            ->name('orders.index');
        Route::post('/','OrderController@store')
            ->name('orders.store');            
        Route::get('/{order}','OrderController@show')
            ->where('order', '[0-9]+')
            ->name('orders.show');
    // Route::get('/{employee}/edit','EmployeeController@edit')
    //     ->middleware(['permission:EmployeesEdit'])
    //     ->where('employee', '[0-9]+')
    //     ->name('employees.edit');
    // Route::match(['put', 'patch'],'/{employee}','EmployeeController@update')
    //     ->middleware(['permission:EmployeesEdit'])
    //     ->where('employee', '[0-9]+')
    //     ->name('employees.update');
    // Route::delete('/{employee}','EmployeeController@destroy')
    //     ->middleware(['permission:EmployeesDelete'])
    //     ->where('employee', '[0-9]+')
    //     ->name('employees.destroy');
    }
);