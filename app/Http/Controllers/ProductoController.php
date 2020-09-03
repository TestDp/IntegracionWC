<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 2/09/2020
 * Time: 9:28 PM
 */

namespace App\Http\Controllers;


class ProductoController extends Controller
{

    public $servicio;

    public function __construct(ServiceApiController $serviceApiController){
        $this->servicio = $serviceApiController;
    }

    public  function  ObtenerProductosWoo(){
       $result =  $this->servicio->Get('/wp-json/wc/v3/products');
        dd($result);
    }

}