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
    public $clienteController;

    public function __construct(OrdenServicio $ordenServicio,ClienteController $clienteController){
        $this->ordenServicio = $ordenServicio;
        $this->clienteController = $clienteController;
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
        $ordenWoo = (object)  $this->ordenServicio->ConsultarOrdenWoo(2845);
        $clienteWoo = (object)$this->clienteController->CrearClienteSagDesdeWoo($ordenWoo->customer_id);
        $xmlOrdenSag = $this->ordenServicio->CrearXMLOrdenSag($ordenWoo,$clienteWoo);
        $result = $this->ordenServicio->GuardarOrdenSAG($xmlOrdenSag);
        dd($result);
    }
}