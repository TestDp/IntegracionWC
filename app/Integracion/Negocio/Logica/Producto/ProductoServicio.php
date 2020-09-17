<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 10/09/2020
 * Time: 5:02 PM
 */

namespace App\Integracion\Negocio\Logica\Producto;


use App\Integracion\Servicios\Rest\Woocommerce\ServiceClientWoo;
use App\Integracion\Servicios\Soap\Sag\ServiceClientSag;

class ProductoServicio
{

    public $clienteServicioWoo;
    public $clienteServicioSag;

    public function __construct(ServiceClientWoo $clienteServicioWoo, ServiceClientSag $clienteServicioSag){
        $this->clienteServicioWoo = $clienteServicioWoo;
        $this->clienteServicioSag = $clienteServicioSag;
    }

    public  function  ConsultarProductosWoo(){
        $result =  $this->clienteServicioWoo->Get('/wp-json/wc/v3/products');
        return $result;
    }

    public  function  ActualizarProductosWoo(){
        $formParams = ['stock_quantity' => '165'];
        $result = $this->clienteServicioWoo->Put('/wp-json/wc/v3/products/588',$formParams);
        return $result;
    }

    public  function CrearProductoWoo(){
        $formaParams = [
           // "id" => 700,
            'name' => 'BROCHAS X2',//sc_detalle_articulo
            'type' => 'simple',
            'regular_price' => '99.50',//Precio 1
            'description' => 'cemento argos',//sv_obs_articulo
            'short_description' => 'nuevo',//sv_obs_articulo
            "sku" => "999",//k_sc_codigo_articulo
            'categories' => [
                [
                    'id' => 15//ka_ni_grupo
                ]
            ],
            'images' => [
                [
                    'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg'
                    // ss_direccion_logo   ejemplo: C:\Users\Servidor\Desktop\FOTOS PRODUCTOS\ABRASIVOS\LIJA  ABRACOL.jpg
                    //Concatenar la url(https://depositolaramada.com/wp-content/uploads/2020/) + el nombre de la imagen VALIDAR CAMPO.
                ]
            ]
        ];
        $result = $this->clienteServicioWoo->Post('/wp-json/wc/v3/products',$formaParams);
        return $result;
    }


    public function ConsultarProductosSAG(){
        $result = $this->clienteServicioSag ->
                    GetConsultaSagJson('select * from articulos where sc_tienda_virtual = "S"');
        return $result;
    }


}