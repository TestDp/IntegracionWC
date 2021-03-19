<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 5/03/2021
 * Time: 1:30 PM
 */

namespace App\Integracion\Comunes;


class Constantes
{
    public static $CODIGOGRUPOSAG = 166;/**Codigo de grupo para produtos simples.SOLO APLICA PARA DISTRIVENUS**/
    public static $MAXPETICIONBACHTWOO = 98;/** Maximo de peticiones en bacht  por defecto en woocomerce **/
    public static $NOMBREHOSTDETALLISTAS = 'DETALLISTAS';
    public static $NOMBREHOSTMAYORISTAS = 'MAYORISTAS';
    public static $NOMBREHOSTDISTRIBUIDORES= 'DISTRIBUIDORES';
    public static $PRECIODETALLISTAS = 'nd_precio8';
    public static $PRECIOMAYORISTAS = 'n_valor_venta_especial';
    public static $PRECIODISTRIBUIDORES = 'nd_precio6';


    public static $URLBASE = '/wp-json/wc/v3';

    /**End pointS Woocomerce*/
    public static $ENDPOINTPRODUCTOS = '/products';
    public static $ENDPOINTORDENES = '/orders';
    public static $ENDPOINTCLIENTES = '/customers';
    public static $ENDPOINTVARIACIONES = '/variations';
    public static $ENDPOINTBATCH = '/batch';

    /**Campos SAG*/
    public static $SKU = 'sku';
    public static $KANIGRUPO = 'ka_ni_grupo';

    /**Campos woocomerce*/
    public static $TYPE = 'type';


}