<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 2/09/2020
 * Time: 9:28 PM
 */

namespace App\Http\Controllers;


use App\Integracion\Negocio\Logica\Producto\ProductoServicio;

class ProductoController extends Controller
{

    public $productoServicio;

    public function __construct(ProductoServicio $productoServicio){
        $this->productoServicio = $productoServicio;
    }

    public function CargaInicialWoo(){
        $this->productoServicio->CargaInicialWoo();
    }
    public  function  ConsultarProductosWoo(){
      $result =  $this->productoServicio->ConsultarProductosWoo(100);
        dd($result);
    }

    public function CrearProductosWoo(){
        $result =  $this->productoServicio->CrearProductoWoo();
        dd($result);
    }
    public function ActualizarProductosWoo(){
        $result =  $this->productoServicio->ActualizarProductosWoo();
        dd($result);
    }

    public  function  ActualizarInventarioProductosWoo(){
        $result =  $this->productoServicio->ActualizarInventarioProductosWoo();
        dd($result);
    }

    public function ConsultarProductosSAG(){
        $result =  $this->productoServicio->ConsultarProductosSAG();
        dd($result);
    }


}