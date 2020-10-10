<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 17/09/2020
 * Time: 8:43 AM
 */

namespace App\Http\Controllers;


use App\Integracion\Negocio\Logica\Producto\OrdenServicio;

class OrdenController extends Controller
{

    public $ordenServicio;

    public function __construct(OrdenServicio $ordenServicio){
        $this->ordenServicio = $ordenServicio;
    }

    public function ConsultarOrdenesWoo(){
        $result =  $this->ordenServicio->ConsultarOrdenesWoo();
        dd($result);
    }
    public function ConsultarOrdenWoo(){
        $result =  $this->ordenServicio->ConsultarOrdenWoo(2842);
        dd($result);
    }

    public function CrearOrdenSagDesdeWoo(){
        //$ordenWoo = (object)  $this->clienteServicio->ConsultarOrdenWoo(2842);
        //$test = $clienteWoo->first_name ;
        //$xmlOrdenSag = $this->clienteServicio->CrearXMLOrdenSag($ordenWoo);
        $xmlOrdenSag = $this->ordenServicio->CrearXMLOrdenSag();
        $result = $this->ordenServicio->GuardarOrdenSAG($xmlOrdenSag);
        dd($result);
    }
}