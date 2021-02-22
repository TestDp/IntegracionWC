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
            }else{
                $nomreImagen = $this->ObtenerNombreImagen($productoSag->ss_direccion_logo);
                $formParams = ['name' => $productoSag->sc_detalle_articulo,
                               'description' => $productoSag->sv_obs_articulo,
                                'images' => [
                                    [
                                        'id'=>$productoWoo['images'][0]['id'],
                                        'src' => env('RUTAIMAGENES').$nomreImagen
                                        // ss_direccion_logo   ejemplo: C:\Users\Servidor\Desktop\FOTOS PRODUCTOS\ABRASIVOS\LIJA  ABRACOL.jpg
                                        //Concatenar la url(https://depositolaramada.com/wp-content/uploads/2020/) + el nombre de la imagen VALIDAR CAMPO.
                                    ]
                                ]
                              ];
                $this->clienteServicioWoo->Put('/wp-json/wc/v3/products/'.$productoWoo['id'],$formParams);
            }
        }
        return "success";
    }

    public  function  ActualizarInventarioProductosWoo($periodo){
        $result  = $this->ConsultarInventarioProductosSAG($periodo);
        $listProductosWoo = $this->ConsultarListaTotalDeProductosWoo();
        foreach ($result as $productoSag){
            $productoWoo =  $listProductosWoo->firstWhere('sku','=',$productoSag->k_sc_codigo_articulo);
            $formParams = ['stock_quantity' => intval($productoSag->n_saldo_actual),
                           'regular_price' => $productoSag->n_valor_venta_normal];
            $this->clienteServicioWoo->Put('/wp-json/wc/v3/products/'.$productoWoo['id'],$formParams);
        }
        return $result;
    }

    public  function  ActualizarInventarioProductosWooBatch($periodo){
        $result  = $this->ConsultarInventarioProductosSAG($periodo);
        $listProductosWoo = $this->ConsultarListaTotalDeProductosWoo();
        $arrayData = [];
        foreach ($result as $productoSag){
            $productoWoo =  $listProductosWoo->firstWhere('sku','=',$productoSag->k_sc_codigo_articulo);
            $arrayData[] = ['id'=>$productoWoo['id'],
                            'stock_quantity' => intval($productoSag->n_saldo_actual),
                            'regular_price' => $productoSag->n_valor_venta_normal];
        }
        $formData = ['update' => $arrayData];
        $this->clienteServicioWoo->Post('/wp-json/wc/v3/products/batch',$formData);
        return $result;
    }

    public  function CrearProductoWoo($productoSag){
        $nomreImagen = $this->ObtenerNombreImagen($productoSag->ss_direccion_logo);
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
                    GetConsultaSagJson("select  sc_detalle_articulo, n_valor_venta_normal,sv_obs_articulo,
                                                       sv_obs_articulo,k_sc_codigo_articulo,
                                                       ka_ni_grupo,ss_direccion_logo 
                                        from articulos where sc_tienda_virtual = 'S'");
        return $result;
    }

    public function ConsultarInventarioProductosSAG($periodo){
        $result = $this->clienteServicioSag ->
        GetConsultaSagJson("select a.k_sc_codigo_articulo, a.n_valor_venta_normal,s.n_saldo_actual
                                     from saldos_articulos as s WITH(NOLOCK)
                                     inner join bodegas as b
                                     on s.ka_nl_bodega = b.ka_nl_bodega
                                     inner join articulos as a
                                     on s.ka_nl_articulo = a.ka_nl_articulo
                                     where b.ka_nl_bodega = 1 and a.sc_tienda_virtual = 'S' and k_sc_periodo =".$periodo);
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

    public function ObtenerNombreImagen($direccionLogo){
        $nomreImagen = 'no-img.png';
        if($direccionLogo != null  && $direccionLogo !="")
        {
            $partesRuta= collect(explode("\\",$direccionLogo));
            $nomreImagen = $partesRuta->last();
        }
        return $nomreImagen;
    }
}