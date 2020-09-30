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

/**Route::get('/', function () {
    return view('welcome');
});**/



//RUTAS WOOCOMERCE
Route::get('/', 'ProductoController@ConsultarProductosWoo')->name('products');
Route::get('/createProduct', 'ProductoController@CrearProductosWoo')->name('createProduct');
Route::get('/updateProducts', 'ProductoController@ActualizarProductosWoo')->name('updateProducts');
Route::get('/getOrders', 'OrdenController@ConsultarOrdenesWoo')->name('getOrders');
Route::get('/customers', 'ClienteController@ConsultarClientesWoo')->name('customers');
Route::get('/generarXML', 'ClienteController@CrearXMLSag')->name('generarXML');


//RUTAS SAG
Route::get('/getProducts', 'ProductoController@ConsultarProductosSAG')->name('getProducts');
Route::get('/saveCustomer', 'ClienteController@GuardarClientesSAG')->name('saveCustomer');

