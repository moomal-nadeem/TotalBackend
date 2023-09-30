<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LegerController;
use App\Http\Controllers\AccRegController;
use App\Http\Controllers\MsgController;
use App\Http\Controllers\CompanyProController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/  

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('fetchAllTrans',[LegerController::class,'fetchAllTrans']);
Route::get('fetchLegerRecords',[LegerController::class,'fetchLegerRecords']);
Route::post('insertForm',[LegerController::class,'insertForm']);
Route::get('ActiveAccounts',[LegerController::class,'ActiveAccounts']);
Route::get('ListAllMsg',[MsgController::class,'ListAllMsg']);
Route::delete('msgDelete/{id}',[MsgController::class,'msgDelete']);
Route::get('getBalance/{id}',[LegerController::class,'getBalance']);
Route::get('ChartAccount',[LegerController::class,'ChartAccount']);
Route::get('getLedgerGroup',[LegerController::class,'getLedgerGroup']);
Route::get('getLedgerEntriesByGroup/{id}',[LegerController::class,'getLedgerEntriesByGroup']);
Route::get('getGroupBalances/{id}',[LegerController::class,'getGroupBalances']);
Route::get('getAccountBalances/{id}',[LegerController::class,'getAccountBalances']);
Route::get('getAccountSummary',[LegerController::class,'getAccountSummary']);
Route::get('getLedgerEntries/{id}',[LegerController::class,'getLedgerEntries']);
Route::get('OneLeger/{id}',[LegerController::class,'OneLeger']);
Route::get('OpenBlc/{id}',[LegerController::class,'OpenBlc']);
Route::get('getByDateRange/{id}', [LegerController::class, 'getByDateRange']);
Route::get('fetchMsgsByAccid/{id}', [MsgController::class, 'fetchMsgsByAccid']);
Route::post('addMsg/{id}', [MsgController::class, 'addMsg']);
Route::get('ListLeger',[LegerController::class,'ListLeger']);
Route::get('ListAccReg',[AccRegController::class,'ListAccReg']);
Route::put('msgEdit/{id}', [MsgController::class,'msgEdit']); 
Route::post('login',[AccRegController::class,'login']);
Route::post('logins',[CompanyProController::class,'logins']);
//pr otected routes
Route::middleware(['auth:sanctum'])->group(function(){
    Route::post('logout',[UserController::class,'logout']);
    // Route::get('user',[UserController::class,'user']);
    // Route::post('change_password',[UserController::class,'change_password']);
});
