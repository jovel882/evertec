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
        Route::get('{order}/pay','OrderController@pay')
            ->where('order', '[0-9]+')
            ->name('orders.pay');
    }
);
Route::group(
    [
        'prefix' => 'transactions', 
        'middleware' => ['auth'],
    ],
    function() {
        Route::get('/receive/{gateway}/{uuid}','TransactionController@receive')
            ->where('uuid', '[0-9a-fA-F]{8}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{4}\-[0-9a-fA-F]{12}')
            ->name('transactions.receive');        
    }
);
Route::get('/notification/unread/{id}', function ($id) {
    $notification = auth()->user()->notifications()->find($id);
    $notification->markAsRead();
    return redirect($notification->data['url']);
})->name('notification.unread');
