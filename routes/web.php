<?php

use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\ExitItemController;
use App\Http\Controllers\Admin\HomeController;
use App\Http\Controllers\Admin\IncomingGoodsController;
use App\Http\Controllers\Admin\ItemController;
use App\Http\Controllers\Admin\LocationController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportItemInController;
use App\Http\Controllers\Admin\ReportItemOutController;
use App\Http\Controllers\Admin\ReportStockController;
use App\Http\Controllers\Admin\StockStoreController;
use App\Http\Controllers\Admin\UniteTypeController;
use App\Http\Controllers\Admin\UsersController;

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

Route::group(['prefix' => 'admin/', 'as' => 'admin.'], function () {
    Route::get('home', [HomeController::class, 'index'])->name('home.index');
    Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::post('profile/store', [ProfileController::class, 'store'])->name('profile.store');
    Route::resource('users', UsersController::class);

    Route::resource('uniteType', UniteTypeController::class)->except('show');
    Route::post('uniteType/import', [UniteTypeController::class, 'import'])->name('uniteType.import');

    Route::resource('location', LocationController::class)->except('show');
    Route::post('location/import', [LocationController::class, 'import'])->name('location.import');

    Route::resource('item', ItemController::class)->except('show');
    Route::post('item/import', [ItemController::class, 'import'])->name('item.import');

    Route::resource('stockStore', StockStoreController::class)->except('show');
    Route::post('stockStore/import', [StockStoreController::class, 'import'])->name('stockStore.import');
    Route::get('stockStore/getLocation', [StockStoreController::class, 'getLocation'])->name('stockStore.getLocation');
    Route::get('stockStore/getItem', [StockStoreController::class, 'getItem'])->name('stockStore.getItem');
    Route::get('stockStore/getUniteType', [StockStoreController::class, 'getUniteType'])->name('stockStore.getUniteType');

    Route::resource('incomingGoods', IncomingGoodsController::class)->except('show');
    Route::get('incomingGoods/loadDataTable', [IncomingGoodsController::class, 'loadDataTable'])->name('incomingGoods.loadDataTable');
    Route::post('incomingGoods/{id}/postCart', [IncomingGoodsController::class, 'postCart'])->name('incomingGoods.postCart');
    Route::put('incomingGoods/{id}/updateCart', [IncomingGoodsController::class, 'updateCart'])->name('incomingGoods.updateCart');
    Route::delete('incomingGoods/{id}/removePost', [IncomingGoodsController::class, 'removePost'])->name('incomingGoods.removePost');
    Route::post('incomingGoods/checkedPost', [IncomingGoodsController::class, 'checkedPost'])->name('incomingGoods.checkedPost');
    Route::post('incomingGoods/checkedPostMultiple', [IncomingGoodsController::class, 'checkedPostMultiple'])->name('incomingGoods.checkedPostMultiple');
    Route::get('incomingGoods/editMultiple', [IncomingGoodsController::class, 'editMultiple'])->name('incomingGoods.editMultiple');
    Route::post('incomingGoods/updateMultiple', [IncomingGoodsController::class, 'updateMultiple'])->name('incomingGoods.updateMultiple');
    Route::get('incomingGoods/loadDataTableEditMultiple', [IncomingGoodsController::class, 'loadDataTableEditMultiple'])->name('incomingGoods.loadDataTableEditMultiple');
    // Route::get('incomingGoods/loadInputStock', [IncomingGoodsController::class, 'loadInputStock'])->name('incomingGoods.loadInputStock');

    Route::resource('exitItem', ExitItemController::class)->except('show');
    Route::get('exitItem/loadDataTable', [ExitItemController::class, 'loadDataTable'])->name('exitItem.loadDataTable');
    Route::post('exitItem/{id}/postCart', [ExitItemController::class, 'postCart'])->name('exitItem.postCart');
    Route::put('exitItem/{id}/updateCart', [ExitItemController::class, 'updateCart'])->name('exitItem.updateCart');
    Route::delete('exitItem/{id}/removePost', [ExitItemController::class, 'removePost'])->name('exitItem.removePost');
    Route::post('exitItem/checkedPost', [ExitItemController::class, 'checkedPost'])->name('exitItem.checkedPost');
    Route::post('exitItem/checkedPostMultiple', [ExitItemController::class, 'checkedPostMultiple'])->name('exitItem.checkedPostMultiple');
    Route::get('exitItem/editMultiple', [ExitItemController::class, 'editMultiple'])->name('exitItem.editMultiple');
    Route::post('exitItem/updateMultiple', [ExitItemController::class, 'updateMultiple'])->name('exitItem.updateMultiple');
    Route::get('exitItem/loadDataTableEditMultiple', [ExitItemController::class, 'loadDataTableEditMultiple'])->name('exitItem.loadDataTableEditMultiple');
    // Route::get('exitItem/loadInputStock', [ExitItemController::class, 'loadInputStock'])->name('exitItem.loadInputStock');

    Route::get('reportStock/index', [ReportStockController::class, 'index'])->name('reportStock.index');
    Route::get('reportStock/export', [ReportStockController::class, 'export'])->name('reportStock.export');

    Route::get('reportItemIn/index', [ReportItemInController::class, 'index'])->name('reportItemIn.index');
    Route::get('reportItemIn/export', [ReportItemInController::class, 'export'])->name('reportItemIn.export');

    Route::get('reportItemOut/index', [ReportItemOutController::class, 'index'])->name('reportItemOut.index');
    Route::get('reportItemOut/export', [ReportItemOutController::class, 'export'])->name('reportItemOut.export');

    Route::resource('configuration', ConfigurationController::class)->except('show');
});
