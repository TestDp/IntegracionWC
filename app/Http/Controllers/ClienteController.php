<?php
/**
 * Created by PhpStorm.
 * User: DPS-J
 * Date: 21/09/2020
 * Time: 8:17 PM
 */

namespace App\Http\Controllers;

use App\Integracion\Negocio\Logica\Producto\ClienteServicio;

class ClienteController extends COntroller
{
    public $clienteServicio;

    public function __construct(ClienteServicio $clienteServicio){
        $this->clienteServicio = $clienteServicio;
    }

    public  function  ConsultarClientesWoo(){
        $result =  $this->clienteServicio->ConsultarClientesWoo();
        dd($result);


    }


    public  function  CrearXMLSag(){

        $this->clienteServicio->CrearXMLSag();

    }

    public  function GuardarClientesSAG(){

        $result =  $this->clienteServicio->GuardarClientesSAG();
        dd($result);
    }
}