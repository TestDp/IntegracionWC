<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 2/09/2020
 * Time: 9:28 PM
 */

namespace App\Http\Controllers;


use App\Integracion\Negocio\Logica\Producto\ProductoServicio;
use Illuminate\Support\Facades\Log;

class ProductoController extends Controller
{

    public $productoServicio;

    public function __construct(ProductoServicio $productoServicio){
        $this->productoServicio = $productoServicio;
    }

    public function InicializarServiceClientWoo($baseUrl, $claveClienteWoo, $claveSecretaWoo){
        $this->productoServicio->InicializarServiceClientWoo($baseUrl, $claveClienteWoo, $claveSecretaWoo);
    }

    public function CargaInicialWoo(){
        $this->productoServicio->CargaInicialWoo();
    }
    public  function  ConsultarProductosWoo(){
      $result =  $this->productoServicio->ConsultarProductosWoo(100,1);
        dd($result);
    }

    public function CrearProductosWoo(){
        $result =  $this->productoServicio->CrearProductoWoo();
        dd($result);
    }

    public function ActualizarProductosWoo($tipoPrecio){
        date_default_timezone_set('America/Bogota');
        $fechaActual = date('Y-m-d');
        $fecha = date('Y-m-d',strtotime($fechaActual . "- 2 days"));
        $result =  $this->productoServicio->ActualizarProductosWoo($fecha,$tipoPrecio);
        Log::info('Actualización y creación de productos en Woocomerce',array('Resultado de la actualización y/o creación' => $result));
    }

    public  function  ActualizarInventarioProductosWoo($tipoPrecio){
        date_default_timezone_set('America/Bogota');
        $Periodo = date('Ym');
        $result =  $this->productoServicio->ActualizarInventarioProductosWoo($Periodo,$tipoPrecio);
        Log::info('Actualizar Inventario de  Productos Woocommerce',array('Inventario actualizado de los siguientes productos' => $result));
        return "proceso terminado";
    }

    public function ConsultarProductosSAG(){
        date_default_timezone_set('America/Bogota');
        $fechaActual = date('Y-m-d');
        $fecha = date('Y-m-d',strtotime($fechaActual . "- 2 days"));
        $result =  $this->productoServicio->ConsultarProductosSAG($fecha);
        dd($result);
    }


}