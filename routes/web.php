<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AccRegController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::post('login',[AccRegController::class,'login']);
Route::get('/userLogin', function () {
    return view('userLogin');
});




Route::view('noaccess', 'noaccess');
Route::group(['middleware'=>['userCheck']],function(){
    Route::view('userDashboard', 'userDashboard');
});








Route::get('/test-database', function () {
    try {
        DB::connection()->getPdo();
        return "Connected to the database!";
    } catch (\Exception $e) {
        return "Unable to connect to the database. Error: " . $e->getMessage();
    }
});

