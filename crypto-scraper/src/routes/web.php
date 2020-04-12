<?php
declare(strict_types=1);

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

Route::redirect('/', '/search/address');

Route::get('/search/address', 'SearchAddress');
Route::get('/search/owner', 'SearchOwner');
Route::get('/search/source', 'SearchSource');

Route::get('/scheduler', 'Scheduler@get');
Route::post('/scheduler/make', 'Scheduler@make');

Route::get('loader/dom/{id}', 'ModalDOM');