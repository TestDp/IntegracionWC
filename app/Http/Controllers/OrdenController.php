<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 17/09/2020
 * Time: 8:43 AM
 */

namespace App\Http\Controllers;


use App\Integracion\Negocio\Logica\Producto\OrdenServicio;
use DateTime;
use Illuminate\Support\Facades\Log;

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

    public function CrearOrdenSagDesdeWoo($idOrdenWoo){
        $ordenWoo = (object)  $this->ordenServicio->ConsultarOrdenWoo($idOrdenWoo);
        $clienteWoo = (object)$this->clienteController->CrearClienteSagDesdeWoo($ordenWoo->customer_id);
        $xmlOrdenSag = $this->ordenServicio->CrearXMLOrdenSag($ordenWoo,$clienteWoo);
        $result = $this->ordenServicio->GuardarOrdenSAG($xmlOrdenSag);
        dd($result);
    }

    public function CrearOrdenesSagDesdeWoo(){
        date_default_timezone_set('America/Bogota');
        $fechaActual = date('Y-m-d\TH:i:s');
        $fechaConsulta = date('Y-m-d\TH:i:s',strtotime($fechaActual . "- 10 days"));
        $ordenesWoo =  $this->ordenServicio->ConsultarOrdenesWooByDate($fechaConsulta);
        $resultados = array();
        foreach ($ordenesWoo as $ordenWoo) {
            $ordenWoo = (object)$ordenWoo;
            $fechas = explode("T", $ordenWoo->date_created);
            $clienteWoo = (object)$this->clienteController->CrearClienteSagDesdeWoo($ordenWoo->customer_id);
            IF ($this->ordenServicio->ValidarOrdenRepetidaSag($ordenWoo->id, $fechas[0]) == null) {
                $xmlOrdenSag = $this->ordenServicio->CrearXMLOrdenSag($ordenWoo, $clienteWoo);
                $resultados[] = $this->ordenServicio->GuardarOrdenSAG($xmlOrdenSag);
            }
        }
        Log::info('Se han creado las ordenes con exito',array('Ordenes Creadas' => $ordenesWoo));
        return "proceso terminado";
    }
}