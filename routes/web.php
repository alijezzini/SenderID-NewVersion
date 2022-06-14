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
    return view('welcome');
});

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/home',  [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('welcome');

Route::get('/vendornotes',[App\Http\Controllers\noteController::class, 'index'])->middleware('auth')->name('searchnote');
// Route::get('urlCountry/{key}',[App\Http\Controllers\senderController::class, 'getOperatorSecond']);
Route::post('/getOperators',[App\Http\Controllers\noteController::class, 'getOperator']);

Route::post('fetchnotes',[App\Http\Controllers\noteController::class, 'getnote']);

Route::post('/deleteNotesFiles',[App\Http\Controllers\noteController::class, 'deleteNotesFiles']);
Route::post('/editNote',[App\Http\Controllers\noteController::class, 'editNote']);
Route::post('/deleteFile',[App\Http\Controllers\noteController::class, 'deleteFile']);
Route::post('/deleteNote',[App\Http\Controllers\noteController::class, 'deleteNote']);
Route::post('/submitNote',[App\Http\Controllers\noteController::class, 'submit']);



Route::get('/addvendor', [App\Http\Controllers\vendorController::class, 'index'])->middleware('auth')->name('addvendor');
Route::post('/submitVendor',[App\Http\Controllers\vendorController::class, 'submit']);
Route::post('/editVendor',[App\Http\Controllers\vendorController::class, 'editVendor']);
Route::post('/deleteVendor',[App\Http\Controllers\vendorController::class, 'deleteVendor']);




Route::post('/submit',[App\Http\Controllers\senderController::class, 'submit']);

Route::post('/deleteSender',[App\Http\Controllers\senderController::class, 'deleteSender']);
Route::post('/editSender',[App\Http\Controllers\senderController::class, 'editSender']);
Route::get('/fetchSenders',[App\Http\Controllers\SearchController::class, 'getsenderTable']);



     /*    Search Senders only loading all vendor operator and sender             */
Route::get('/searchsenders', [App\Http\Controllers\SearchController::class, 'searchall'])
    ->middleware('auth')->name('searchall');
Route::post('searchsenders/lol', [App\Http\Controllers\SearchController::class, 'lol'])
->middleware('auth')->name('searchall');



     /*    Search vendor notes only loading all vendor operator and sender             */
Route::get('/searchvendornotes', [App\Http\Controllers\SearchController::class, 'searchallnotes'])->middleware('auth')->name('searchall');
Route::get('/senders', [App\Http\Controllers\SearchController::class, 'searchsender'])->middleware('auth')->name('searchsender');
Route::resource('column-searching', 'ColumnSearchingController');

