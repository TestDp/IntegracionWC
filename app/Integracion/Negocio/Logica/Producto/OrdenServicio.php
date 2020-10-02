<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 17/09/2020
 * Time: 8:45 AM
 */

namespace App\Integracion\Negocio\Logica\Producto;


use App\Integracion\Servicios\Rest\Woocommerce\ServiceClientWoo;
use App\Integracion\Servicios\Soap\Sag\ServiceClientSag;

class OrdenServicio
{

    public $serviceClientWoo;
    public $serviceClientSag;

    public function __construct(ServiceClientWoo $serviceClientWoo, ServiceClientSag $serviceClientSag){
        $this->serviceClientWoo = $serviceClientWoo;
        $this->serviceClientSag = $serviceClientSag;
    }

    public function  ConsultarOrdenesWoo(){
        $result =  $this->serviceClientWoo->Get('/wp-json/wc/v3/orders');
        return $result;
    }

    public function  ConsultarOrdenWoo($idOrdenWoo){
        $result =  $this->serviceClientWoo->Get('/wp-json/wc/v3/orders/'.$idOrdenWoo);
        return $result;
    }
}