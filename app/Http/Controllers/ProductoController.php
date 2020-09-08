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

    public function CrearProductosWoo(){
        $formaParams = [
            'name' => 'Cemento x 50kg',
            'type' => 'simple',
            'regular_price' => '99.50',
            'description' => 'cemento argos',
            'short_description' => 'nuevo',
            'categories' => [
                [
                    'id' => 15
                ]
            ],
            'images' => [
                [
                    'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg'
                ],
                [
                    'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg'
                ]
            ]
        ];
        $result =  $this->servicio->Post('/wp-json/wc/v3/products',$formaParams);
        dd($result);
    }

}