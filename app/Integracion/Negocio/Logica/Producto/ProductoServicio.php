<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 10/09/2020
 * Time: 5:02 PM
 */

namespace App\Integracion\Negocio\Logica\Producto;


use App\Integracion\Comunes\Constantes;
use App\Integracion\Servicios\Rest\Woocommerce\ServiceClientWoo;
use App\Integracion\Servicios\Soap\Sag\ServiceClientSag;


class ProductoServicio
{

    public $clienteServicioWoo;
    public $clienteServicioSag;

  public function __construct(ServiceClientSag $clienteServicioSag,ServiceClientWoo $clienteServicioWoo){
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
        $result =  $this->clienteServicioWoo->Get(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS.'?per_page='.$cantResgiXpagina.'&&page='.$nroPagina);
        return $result;
    }


    public  function  ActualizarProductosWoo($fecha){
        $listProductosSag  = collect($this->ConsultarProductosSAG($fecha));
        $listProductosWoo = $this->ConsultarListaTotalDeProductosWoo();
        $listProductosVariablesWoo = $listProductosWoo->where(Constantes::$TYPE,'=','variable');
        $listaVariacionesProductosWoo = $this->ObtenerVariacionesProductosWoo($listProductosVariablesWoo);
        $result = collect();
        foreach ($listProductosSag as $productovariableSag){
            $productoWooVariacion = $listaVariacionesProductosWoo->firstWhere(Constantes::$SKU, '=', $productovariableSag->k_sc_codigo_articulo);
            $productoWoo = null;
            if(! is_null($productoWooVariacion)){
                $idPadre =  $this->ObtenerIdPadre($productoWooVariacion['_links']['up'][0]['href']);
                $productoWoo =  $listProductosVariablesWoo->where('id','=',$idPadre)->first();
            }else{
                $productoWoo =  $listProductosVariablesWoo->where(Constantes::$SKU,'=',$productovariableSag->ss_descripcion_referente)->first();
            }

            if(is_null($productoWoo)){
                $productoVariableWoo = $this->CrearProductoVariableWoo($productovariableSag);
                $variacionProducto = $this->CrearProductoVariacionWoo($productovariableSag,$productoVariableWoo['id']);
                $listProductosVariablesWoo->push($productoVariableWoo);
                $listaVariacionesProductosWoo->push($variacionProducto);
                $resultOperacion = collect($productoVariableWoo);
                $result = $result->concat($resultOperacion);
                $resultOperacion = collect($variacionProducto);
                $result = $result->concat($resultOperacion);
            }
            else {

                if (is_null($productoWooVariacion)) {
                    $variacionProducto = $this->CrearProductoVariacionWoo($productovariableSag, $productoWoo['id']);
                    $listaVariacionesProductosWoo->push($variacionProducto);
                    $resultOperacion = collect($variacionProducto);
                    $result = $result->concat($resultOperacion);
                }
                else
                {
                    $formParams = [ 'description' => ucfirst(strtolower($productovariableSag->sv_obs_articulo)),
                                    'purchase_note'=>$productovariableSag->codigo_articulo_principal];
                    $resultOperacion = collect($this->clienteServicioWoo->Put(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS.'/'. $productoWoo['id'], $formParams));
                    $result = $result->concat($resultOperacion);
                }
            }
        }

        return $result;
    }

    public  function  ActualizarInventarioProductosWoo($periodo)
    {
        $inventarioProductosSAG  = collect($this->ConsultarInventarioProductosSAG($periodo));
        $listProductosWoo = $this->ConsultarListaTotalDeProductosWoo();
        $listProductosVariablesWoo = $listProductosWoo->where(Constantes::$TYPE,'=','variable');
        $listaVariacionesProductosWoo = $this->ObtenerVariacionesProductosWoo($listProductosVariablesWoo);
        $result = $this->ActualizarInventarioProductoWooVariable($listProductosVariablesWoo,$listaVariacionesProductosWoo,
            $inventarioProductosSAG);
        //return $result;
        return $inventarioProductosSAG;
    }

    public function ObtenerVariacionesProductosWoo($listProductosWooVariables){
        $listaVariacionesWoo =  [];
        foreach ($listProductosWooVariables as $productoWoo){
            $listTem =  $this->clienteServicioWoo
                ->Get(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS.'/'.$productoWoo['id'].
                    Constantes::$ENDPOINTVARIACIONES.'?per_page=100');
            $listaVariacionesWoo= array_merge($listaVariacionesWoo,$listTem);
        }
        return collect($listaVariacionesWoo);
    }
    /**
     * retorna un arreglo con la informacion del producto para actualizar
     */
    public function ObtenerArrayProducto($productoSag,$idProductoWoo)
    {
        $formParams = ['id'=>$idProductoWoo,
            'stock_quantity' => intval($productoSag->n_saldo_actual),
            'regular_price' => $productoSag->precioDetallista];
        return $formParams;
    }

    public function ActualizarInventarioProductoWooVariable($listProductosWooVariables,$listaVariacionesProductosWoo,
                                                           $listProducVariablesSAG)
    {
        $result = collect();
        foreach ($listProductosWooVariables as $productoWoo){
            $variacionesXProducto =  $listaVariacionesProductosWoo->whereIn('id',$productoWoo['variations']);
            $cantidadDeVariaciones = count($variacionesXProducto);
            $tamanioArrayProdutoVariable =0;
            $contadorProductosVariables=0;
            $arrayData = [];
            foreach ($variacionesXProducto as $variacionProducto){
                $productoSAG = $listProducVariablesSAG->firstWhere('k_sc_codigo_articulo','=',$variacionProducto[Constantes::$SKU]);
                if($productoSAG != null) {
                    $formParams = $this->ObtenerArrayProducto($productoSAG,$variacionProducto['id']);
                    $arrayData[] = $formParams;
                    $tamanioArrayProdutoVariable++;
                }
                $contadorProductosVariables++;
                if(($tamanioArrayProdutoVariable > Constantes::$MAXPETICIONBACHTWOO ||
                        $contadorProductosVariables == $cantidadDeVariaciones) && $tamanioArrayProdutoVariable > 0)
                {
                    $formData = ['update' => $arrayData];
                    $resultOperacion = collect( $this->clienteServicioWoo
                        ->post(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS. '/' .
                            $productoWoo['id']. Constantes::$ENDPOINTVARIACIONES.Constantes::$ENDPOINTBATCH , $formData));
                    $result = $result->concat($resultOperacion);
                    $arrayData = [];
                    $tamanioArrayProdutoVariable = 0;
                }
            }
        }
        return $result;
    }

    public function ActuaizarInventarioProductoWooSimple($listProductosSimpleWoo,$listProductosSimplesSAG,$tipoPrecio){
        $contadorProductosSimples = 0;
        $totalProductosSimplesSAG = count($listProductosSimplesSAG);
        $tamanioArrayProdutoSimple =0;
        $arrayData = [];
        foreach ($listProductosSimplesSAG as $productoSag){
            $productoWoo =  $listProductosSimpleWoo->firstWhere(Constantes::$SKU,'=',$productoSag->k_sc_codigo_articulo);
            if($productoWoo != null) {
                $formParams = $this->ObtenerArrayProducto($productoSag,$productoWoo['id'],$tipoPrecio);
                $arrayData[] = $formParams;
                $tamanioArrayProdutoSimple++;
            }
            $contadorProductosSimples++;
            if(($tamanioArrayProdutoSimple > Constantes::$MAXPETICIONBACHTWOO ||
                    $contadorProductosSimples == $totalProductosSimplesSAG) && $tamanioArrayProdutoSimple > 0)
            {
                $formData = ['update' => $arrayData];
                $this->clienteServicioWoo->Post(Constantes::$URLBASE.
                    Constantes::$ENDPOINTPRODUCTOS.Constantes::$ENDPOINTBATCH,$formData);
                $arrayData = [];
                $tamanioArrayProdutoSimple = 0;
            }
        }
    }

    //Metodo para crear un producto simple en woocomerce
    public  function CrearProductoWoo($productoSag,$tipoPrecio){
        $nomreImagen = $this->ObtenerNombreImagen($productoSag->ss_direccion_logo);
        $formaParams = [
            'name' => $productoSag->sc_detalle_articulo,
            'type' => 'simple',
            'regular_price' => (Constantes::$PRECIODETALLISTAS == $tipoPrecio)? $productoSag->precioDetallista:
                               (Constantes::$NOMBREHOSTMAYORISTAS == $tipoPrecio)?$productoSag->precioMayorista:
                                $productoSag->precioDistribuidor,
            'description' => ucfirst(strtolower($productoSag->sv_obs_articulo)),
            'short_description' => ucfirst(strtolower($productoSag->sv_obs_articulo)),
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
        $result = $this->clienteServicioWoo->Post(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS,$formaParams);
        return $result;
    }


    public  function CrearProductoVariacionWoo($productoSag,$id){
        $data = [
            'regular_price' => $productoSag->precioDetallista,
            "sku" => $productoSag->k_sc_codigo_articulo,// k_sc_codigo_articulo hace referencia al codigo de barras de sag
            'manage_stock' => true,
            'attributes' => [
                    [
                        'id' => 3,
                         'name' => 'talla',
                         'option' => $productoSag->talla,
                    ],
                    [
                        'id' => 2,
                        'name' => 'color',
                        'option' => $productoSag->descripcion_color,
                    ]
            ]
        ];
        $result = $this->clienteServicioWoo->post(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS.'/'.$id.'/variations', $data);
        return $result;
    }

    public  function CrearProductoVariableWoo($productoSag){
        $nomreImagen = 'no-img.jpg';
        $formaParams = [
            'name' => $productoSag->sc_detalle_articulo,
            'type' => 'variable',
            'regular_price' => $productoSag->precioDetallista,
            'short_description' => ucfirst(strtolower($productoSag->sv_obs_articulo)),
            "sku" => $productoSag->ss_descripcion_referente,
            'purchase_note'=>$productoSag->codigo_articulo_principal,
            'manage_stock' => false,
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
            ],
            'attributes' => [
                [
                    'id' => 3,
                    'name' => 'talla',
                    'position' => 0,
                    'visible' => true,
                    'variation' => true,
                    'options' => Constantes::$TALLAS
                ],
                [
                    'id' => 2,
                    'name' => 'color',
                    'position' => 1,
                    'visible' => true,
                    'variation' => true,
                    'options' => Constantes::$COLORES
                ]
            ]
        ];
        $result = $this->clienteServicioWoo->Post(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS,$formaParams);
        return $result;
    }


    public function ConsultarProductosSAG( $fecha ){
        $result = $this->clienteServicioSag ->
        GetConsultaSagJson("SELECT concat(a.ss_detalle_artic2,' ',sc_referencia) as sc_detalle_articulo,
                            a.k_sc_codigo_articulo as codigo_articulo_principal ,                            
                            a.nd_valor_venta4 as precioDetallista,sv_obs_articulo, a.sc_referencia AS ss_descripcion_referente,sv_obs_articulo,
                            cb.ss_codigo_barras AS k_sc_codigo_articulo,ka_ni_grupo,ss_direccion_logo,ka_ni_grupo,  t.ss_talla as talla,  
                            c.ss_color as codigo_color,  c.ss_color_largo as descripcion_color
                            FROM articulos a With(NoLock) 
                            INNER JOIN sta_sku k With(NoLock) 
                            ON a.ka_nl_articulo = k.ka_nl_articulo
                            INNER JOIN sta_tallas t With(NoLock) 
                            ON k.ka_nl_talla = t.ka_nl_talla  
                            INNER JOIN sta_colores c With(NoLock) 
                            ON k.ka_nl_color = c.ka_nl_color  
                            INNER JOIN art_cod_barras cb 
                            ON a.ka_nl_articulo = cb.ka_nl_articulo and c.ka_nl_color = cb.ka_nl_color AND cb.ka_nl_talla = t.ka_nl_talla  
                            WHERE t.ss_talla <> 'SURT' and c.ss_color <> 'SURT' and a.sc_tienda_virtual = 'S' and cb.ss_codigo_barras <> ''  and 
                            a.dd_fecha_ult_modificacion >"."'". $fecha."'");
        return $result;
    }

    public function ConsultarInventarioProductosSAG($periodo){
        $result = $this->clienteServicioSag ->
        GetConsultaSagJson("SELECT cb.ss_codigo_barras as k_sc_codigo_articulo, a.sc_referencia, a.nd_valor_venta4 as precioDetallista,  s.ss_talla as talla,  c.ss_color as codigo_color,
							c.ss_color_largo as descripcion_color,  s.nd_cantidad as n_saldo_actualReal,  a.ka_ni_grupo, a.sc_referencia as ss_descripcion_referente,
							s.nd_cantidad -(SELECT ISNULL(sum(saldos_pedidos.n_saldo_actual),0)
											FROM saldos_pedidos With(NoLock) INNER JOIN movimientos_items With(NoLock) 
											ON saldos_pedidos.ka_nl_movimiento_item = movimientos_items.ka_nl_movimiento_item
											INNER JOIN movimientos With(NoLock) ON movimientos_items.ka_nl_movimiento = movimientos.ka_nl_movimiento
											INNER JOIN fuentes With(NoLock) ON movimientos.ka_ni_fuente = fuentes.ka_ni_fuente
											WHERE saldos_pedidos.k_sc_periodo = ".$periodo ." AND 
											fuentes.k_sc_codigo_fuente = 'PW' AND movimientos.sc_anulado = 'N' AND movimientos_items.ka_nl_articulo = a.ka_nl_articulo AND 
											saldos_pedidos.n_saldo_actual > 0 AND movimientos_items.ss_talla = s.ss_talla AND movimientos_items.ss_color = s.ss_color) as n_saldo_actual
							FROM  articulos a With(NoLock) INNER JOIN art_cod_barras cb
							ON a.ka_nl_articulo = cb.ka_nl_articulo INNER JOIN sta_colores c With(Nolock)
							ON cb.ka_nl_color = c.ka_nl_color INNER JOIN sta_tallas t With(NoLock)
							ON cb.ka_nl_talla = t.ka_nl_talla INNER JOIN saldos_articulos_bin s  With(NoLock)
							ON  s.ka_nl_articulo = a.ka_nl_articulo AND s.ss_talla = t.ss_talla and s.ss_color = c.ss_color
							WHERE t.ss_talla <> 'SURT' and c.ss_color <> 'SURT' and a.sc_tienda_virtual = 'S' AND cb.ss_codigo_barras <> '' AND s.ka_nl_bodega = ". Constantes::$IDBODEGA ."
							AND s.nd_cantidad > 0  AND s.ss_periodo = ".$periodo);
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
        $nomreImagen = 'no-img.jpg';
        if($direccionLogo != null  && $direccionLogo !="")
        {
            $partesRuta= collect(explode("\\",$direccionLogo));
            $nomreImagen = $partesRuta->last();
        }
        return $nomreImagen;
    }

    public function ObtenerIdPadre($direccionPadre){
        $partesLink= collect(explode("/",$direccionPadre));
        $idPadre = $partesLink->last();
        return $idPadre;
    }

    public function ObtenerProductoWoo($idProductoWoo){
        $result =  (object)$this->clienteServicioWoo->Get(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS.'/'.$idProductoWoo);
        return $result;
    }
}