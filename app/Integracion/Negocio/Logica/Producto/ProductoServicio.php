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
use phpDocumentor\Reflection\Types\Collection;

class ProductoServicio
{

    public $clienteServicioWoo;
    public $clienteServicioSag;

    public function __construct(ServiceClientWoo $clienteServicioWoo, ServiceClientSag $clienteServicioSag){
        $this->clienteServicioWoo = $clienteServicioWoo;
        $this->clienteServicioSag = $clienteServicioSag;
    }

    public function CargaInicialWoo(){
        $result  = $this->ConsultarProductosSAG();
        foreach ($result as $productoSag){
            $this->CrearProductoWoo($productoSag);
        }
    }

    public  function  ConsultarProductosWoo($cantResgiXpagina,$nroPagina){
        $result =  $this->clienteServicioWoo->Get('/wp-json/wc/v3/products?per_page='.$cantResgiXpagina.'&&page='.$nroPagina);
        return $result;
    }


    public  function  ActualizarProductosWoo(){
        $listProductosSag  = $this->ConsultarProductosSAG();
        $listProductosWoo = $this->ConsultarListaTotalDeProductosWoo();
        foreach ($listProductosSag as $productoSag){
            $productoWoo =  $listProductosWoo->firstWhere('sku','=',$productoSag->k_sc_codigo_articulo);
            if(is_null($productoWoo)){
                $this->CrearProductoWoo($productoSag);
            }
        }
        return "success";
    }

    public  function  ActualizarInventarioProductosWoo($periodo){
        $result  = $this->ConsultarInventarioProductosSAG($periodo);
        $listProductosWoo = $this->ConsultarListaTotalDeProductosWoo();
        foreach ($result as $productoSag){
            $productoWoo =  $listProductosWoo->firstWhere('sku','=',$productoSag->k_sc_codigo_articulo);
            $formParams = ['stock_quantity' => $productoSag->n_saldo_actual];
            $this->clienteServicioWoo->Put('/wp-json/wc/v3/products/'.$productoWoo['id'],$formParams);
        }

        return $result;

    }
    public  function CrearProductoWoo($productoSag){
        $rutaImagen = $productoSag->ss_direccion_logo;
        $nomreImagen = 'no-img.png';
        if($rutaImagen != null  && $rutaImagen !="")
        {
            $partesRuta= collect(explode("\\",$rutaImagen));
            $nomreImagen = $partesRuta->last();
        }
        $formaParams = [
            'name' => $productoSag->sc_detalle_articulo,
            'type' => 'simple',
            'regular_price' => $productoSag->n_valor_venta_normal,
            'description' => $productoSag->sv_obs_articulo,
            'short_description' => $productoSag->sv_obs_articulo,
            "sku" => $productoSag->k_sc_codigo_articulo,
            'manage_stock' => true,
            'categories' => [
                [
                    'id'=> $productoSag->ka_ni_grupo
                ]
            ],
            'images' => [
                [
                    'src' => env('RUTAIMAGENES').$nomreImagen
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
                    GetConsultaSagJson("select * from articulos where sc_tienda_virtual = 'S'");
        return $result;
    }

    public function ConsultarInventarioProductosSAG($periodo){
        $result = $this->clienteServicioSag ->
        GetConsultaSagJson("select a.k_sc_codigo_articulo,s.* from saldos_articulos as s WITH(NOLOCK)
                                     inner join bodegas as b
                                     on s.ka_nl_bodega = b.ka_nl_bodega
                                     inner join articulos as a
                                     on s.ka_nl_articulo = a.ka_nl_articulo
                                     where b.ka_nl_bodega = 67 and k_sc_periodo =".$periodo);
        return $result;
    }

    public function ConsultarListaTotalDeProductosWoo(){
        $page=1;
        $tieneProducto = true;
        $listProductosWoo = collect();
        while($tieneProducto){
            $lisProductosWooResult= collect($this->ConsultarProductosWoo(100,$page));
            $listProductosWoo = $listProductosWoo->concat($lisProductosWooResult);
            if($lisProductosWooResult->count() > 0)
            {
                $page = $page + 1;
            }
            else {
                $tieneProducto =false;
            }
        }
        return $listProductosWoo;
    }
}