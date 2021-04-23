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

    public function InicializarServiceClientWoo($baseUrl, $claveClienteWoo, $claveSecretaWoo){
        $this->clienteServicioWoo->InicializarServiceClientWoo($baseUrl, $claveClienteWoo, $claveSecretaWoo);
    }
 /* public function __construct(ServiceClientSag $clienteServicioSag){
        $this->clienteServicioWoo = new ServiceClientWoo('https://detallistas.distrivenus.com/',
            env('CLAVE_CLIENTE_DETALLISTAS'),env('CLAVE_SECRETA_DETALLISTAS'));
        $this->clienteServicioSag = $clienteServicioSag;
    }*/

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


    public  function  ActualizarProductosWoo($fecha,$tipoPrecio){

        $listProductosSag  = collect($this->ConsultarProductosSAG($fecha));
        $listProductosWoo = $this->ConsultarListaTotalDeProductosWoo();

        $listProductosVariablesWoo = $listProductosWoo->where(Constantes::$TYPE,'=','variable');
        $listProductosSimpleWoo = $listProductosWoo->where(Constantes::$TYPE,'=','simple');

        $listProductosSimplesSAG = $listProductosSag->where(Constantes::$KANIGRUPO,'=',Constantes::$CODIGOGRUPOSAG);
        $listProducVariablesSAG = $listProductosSag->where(Constantes::$KANIGRUPO,'!=',Constantes::$CODIGOGRUPOSAG);

        $listaVariacionesProductosWoo = $this->ObtenerVariacionesProductosWoo($listProductosVariablesWoo);

        foreach ($listProductosSimplesSAG as $productoSag){
            $productoWoo =  $listProductosSimpleWoo->firstWhere(Constantes::$SKU,'=',$productoSag->k_sc_codigo_articulo);
            if(is_null($productoWoo)){
                $this->CrearProductoWoo($productoSag,$tipoPrecio);
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
                $this->clienteServicioWoo->Put(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS.'/'.$productoWoo['id'],$formParams);
            }
        }

        foreach ($listProducVariablesSAG as $productovariableSag){
            $productoWooVariacion = $listaVariacionesProductosWoo->firstWhere(Constantes::$SKU, '=', $productovariableSag->k_sc_codigo_articulo);
            $productoWoo = null;
            if(! is_null($productoWooVariacion)){
                $idPadre =  $this->ObtenerIdPadre($productoWooVariacion['_links']['up'][0]['href']);
                $productoWoo =  $listProductosVariablesWoo->where('id','=',$idPadre)
                    ->where(Constantes::$SKU, '=','')->first();
            }else{
                $productoWoo =  $listProductosVariablesWoo->where('name','=',$productovariableSag->ss_descripcion_referente)
                    ->where(Constantes::$SKU, '=','')->first();
            }

            if(is_null($productoWoo)){
                $productoVariableWoo = $this->CrearProductoVariableWoo($productovariableSag,$tipoPrecio);
                $variacionProducto = $this->CrearProductoVariacionWoo($productovariableSag,$productoVariableWoo['id'],$tipoPrecio);
                $listProductosVariablesWoo->push($productoVariableWoo);
                $listaVariacionesProductosWoo->push($variacionProducto);
            }
            else {
                $productoWooVariacion = $listaVariacionesProductosWoo->firstWhere(Constantes::$SKU, '=', $productovariableSag->k_sc_codigo_articulo);
                if (is_null($productoWooVariacion)) {
                    $variacionProducto = $this->CrearProductoVariacionWoo($productovariableSag, $productoWoo['id'],$tipoPrecio);
                    $listaVariacionesProductosWoo->push($variacionProducto);
                }
                else
                {
                    //NO BORRAR
                    //$formParams = ['name' => $productovariableSag->sc_detalle_articulo,
                   //                 'description' => $productovariableSag->sv_obs_articulo];
                   // $this->clienteServicioWoo->Put(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS.'/'. $productoWoo['id'].'/variations/'.$productoWooVariacion['id'], $formParams);

                }
            }
        }

        return "success";
    }

    public  function  ActualizarInventarioProductosWoo($periodo,$tipoPrecio){

        $result  = collect($this->ConsultarInventarioProductosSAG($periodo));
        $listProductosWoo = $this->ConsultarListaTotalDeProductosWoo();
        $listProductosVariablesWoo = $listProductosWoo->where(Constantes::$TYPE,'=','variable');
        $listProductosSimpleWoo = $listProductosWoo->where(Constantes::$TYPE,'=','simple');

        $listProductosSimplesSAG = $result->where(Constantes::$KANIGRUPO,'=',Constantes::$CODIGOGRUPOSAG);
        $listProducVariablesSAG = $result->where(Constantes::$KANIGRUPO,'!=',Constantes::$CODIGOGRUPOSAG);

        $listaVariacionesProductosWoo = $this->ObtenerVariacionesProductosWoo($listProductosVariablesWoo);

        $this->ActuaizarInventarioProductoWooSimple($listProductosSimpleWoo,$listProductosSimplesSAG,$tipoPrecio);
        $this->ActuaizarInventarioProductoWooVariable($listProductosVariablesWoo,$listaVariacionesProductosWoo,
            $listProducVariablesSAG,$tipoPrecio);
        return $result;
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
    public function ObtenerArrayProducto($productoSag,$idProductoWoo,$tipoPrecio)
    {
        $formParams = ['id'=>$idProductoWoo,
            'stock_quantity' => intval($productoSag->n_saldo_actual),
            'regular_price' =>(Constantes::$PRECIODETALLISTAS == $tipoPrecio)? $productoSag->precioDetallista:
                              (Constantes::$NOMBREHOSTMAYORISTAS == $tipoPrecio)?$productoSag->precioMayorista:
                                  $productoSag->precioDistribuidor];
        return $formParams;
    }

    public function ActuaizarInventarioProductoWooVariable($listProductosWooVariables,$listaVariacionesProductosWoo,
                                                           $listProducVariablesSAG,$tipoPrecio)
    {
        foreach ($listProductosWooVariables as $productoWoo){
            $variacionesXProducto =  $listaVariacionesProductosWoo->whereIn('id',$productoWoo['variations']);
            $cantidadDeVariaciones = count($variacionesXProducto);
            $tamanioArrayProdutoVariable =0;
            $contadorProductosVariables=0;
            $arrayData = [];
            foreach ($variacionesXProducto as $variacionProducto){
                $productoSAG = $listProducVariablesSAG->firstWhere('k_sc_codigo_articulo','=',$variacionProducto[Constantes::$SKU]);
                if($productoSAG != null) {
                    $formParams = $this->ObtenerArrayProducto($productoSAG,$variacionProducto['id'],$tipoPrecio);
                    $arrayData[] = $formParams;
                    $tamanioArrayProdutoVariable++;
                }
                $contadorProductosVariables++;
                if(($tamanioArrayProdutoVariable > Constantes::$MAXPETICIONBACHTWOO ||
                        $contadorProductosVariables == $cantidadDeVariaciones) && $tamanioArrayProdutoVariable > 0)
                {
                    $formData = ['update' => $arrayData];
                    $this->clienteServicioWoo
                        ->post(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS. '/' .
                            $productoWoo['id']. Constantes::$ENDPOINTVARIACIONES.Constantes::$ENDPOINTBATCH , $formData);
                    $arrayData = [];
                    $tamanioArrayProdutoVariable = 0;
                }
            }
        }
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

    public  function CrearProductoWoo($productoSag,$tipoPrecio){
        $nomreImagen = $this->ObtenerNombreImagen($productoSag->ss_direccion_logo);
        $formaParams = [
            'name' => $productoSag->sc_detalle_articulo,
            'type' => 'simple',
            'regular_price' => (Constantes::$PRECIODETALLISTAS == $tipoPrecio)? $productoSag->precioDetallista:
                               (Constantes::$NOMBREHOSTMAYORISTAS == $tipoPrecio)?$productoSag->precioMayorista:
                                $productoSag->precioDistribuidor,
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
        $result = $this->clienteServicioWoo->Post(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS,$formaParams);
        return $result;
    }


    public  function CrearProductoVariacionWoo($productoSag,$id,$tipoPrecio){

        $data = [
            'regular_price' => (Constantes::$PRECIODETALLISTAS == $tipoPrecio)? $productoSag->precioDetallista:
                (Constantes::$NOMBREHOSTMAYORISTAS == $tipoPrecio)?$productoSag->precioMayorista:
                    $productoSag->precioDistribuidor,
            "sku" => $productoSag->k_sc_codigo_articulo,
            'manage_stock' => true,
            'attributes' => [
                    [
                        'id' => 1,
                         'name' => 'talla',
                         'option' => $productoSag->Talla,
                    ]
            ]
        ];
        $result = $this->clienteServicioWoo->post(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS.'/'.$id.'/variations', $data);
        return $result;
    }

    public  function CrearProductoVariableWoo($productoSag,$tipoPrecio){
        $nomreImagen = $this->ObtenerNombreImagen($productoSag->ss_direccion_logo);
        $formaParams = [
            'name' => $productoSag->ss_descripcion_referente,
            'type' => 'variable',
            'regular_price' => (Constantes::$PRECIODETALLISTAS == $tipoPrecio)? $productoSag->precioDetallista:
                (Constantes::$NOMBREHOSTMAYORISTAS == $tipoPrecio)?$productoSag->precioMayorista:
                    $productoSag->precioDistribuidor,
            'description' => $productoSag->sv_obs_articulo,
            'short_description' => $productoSag->sv_obs_articulo,
            "sku" => '',
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
                    'id' => 1,
                    'name' => 'talla',
                    'position' => 0,
                    'visible' => true,
                    'variation' => true,
                    'options' => ['21','22','23','24','25','26','27','28','29','30','31','32','33','34',
                                    '35','36','37','38','39','40','41','42','43','44','45','46','47','48']
                ]
            ]
        ];
        $result = $this->clienteServicioWoo->Post(Constantes::$URLBASE.Constantes::$ENDPOINTPRODUCTOS,$formaParams);
        return $result;
    }


    public function ConsultarProductosSAG( $fecha ){
        $result = $this->clienteServicioSag ->
        GetConsultaSagJson("SELECT sc_detalle_articulo, nd_precio4 as precioDistribuidor,n_valor_venta_especial as precioMayorista,
                            nd_precio8 as precioDetallista,sv_obs_articulo, a.sc_referencia AS ss_descripcion_referente,sv_obs_articulo,
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
                            WHERE a.sc_tienda_virtual = 'S' and
                            a.dd_fecha_ult_modificacion >"."'". $fecha."'");
        return $result;
    }

    public function ConsultarInventarioProductosSAG($periodo){
        $result = $this->clienteServicioSag ->
        GetConsultaSagJson("SELECT cb.ss_codigo_barras as k_sc_codigo_articulo, a.sc_referencia, 
                             a.nd_precio4 as precioDistribuidor,  a.n_valor_venta_especial as precioMayorista,  
                            a.nd_precio8 as precioDetallista,  s.ss_talla as talla,  c.ss_color as codigo_color,
                            c.ss_color_largo as descripcion_color,  s.nd_cantidad as saldo_actual,  a.ka_ni_grupo, a.sc_referencia as ss_descripcion_referente
                            FROM saldos_articulos_bin s  With(NoLock) 
                            INNER JOIN articulos a With(NoLock) ON 
                            s.ka_nl_articulo = a.ka_nl_articulo
                            INNER JOIN sta_colores c With(Nolock) 
                            ON s.ss_color = c.ss_color
                            INNER JOIN art_cod_barras cb 
                            ON a.ka_nl_articulo = cb.ka_nl_articulo and c.ka_nl_color = cb.ka_nl_color
                            INNER JOIN sta_tallas t With(NoLock) 
                            ON cb.ka_nl_talla = t.ka_nl_talla  
                            WHERE a.sc_tienda_virtual = 'S' AND 
                            s.ka_nl_bodega = 1 AND 
                            s.nd_cantidad > 0  AND s.ss_periodo = ".$periodo);
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
}