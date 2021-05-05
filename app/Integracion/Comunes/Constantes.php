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
    public static $MAXPETICIONBACHTWOO = 98;/** Maximo de peticiones en bacht  por defecto en woocomerce **/
    public static $URLBASE = '/wp-json/wc/v3';
    public static  $NUMERODEDIASCONSULTA = '- 3 days';

    /**End pointS Woocomerce*/
    public static $ENDPOINTPRODUCTOS = '/products';
    public static $ENDPOINTORDENES = '/orders';
    public static $ENDPOINTCLIENTES = '/customers';
    public static $ENDPOINTVARIACIONES = '/variations';
    public static $ENDPOINTBATCH = '/batch';

    /**Campos SAG*/
    public static $SKU = 'sku';
    public static $KANIGRUPO = 'ka_ni_grupo';
    public static $CODIGOBODEGA = '08';// el id de la bodega se utiliza en la orden
    public static $IDBODEGA = '20'; // el codigo se utiliza en la consulta del inventario

    /**Campos woocomerce*/
    public static $TYPE = 'type';

    public static  $TALLAS = ['S','M', 'L','XS', 'XXL','SURT','XL','04', '06', '08','10','12','14','16','U'];
    public static  $COLORES = ['Azul','Amarillo','Rojo','Surtido','Blanco','Negro','Crudo','Verde','Morado','Mostaza',
        'Terracota','Rosado','Militar','Guayaba','Rey','Estampado','Cafe','Indigo','Camel','Caqui','Salmon','Coral',
        'Palo de Rosa','Fucsia','Verde Jade','Verde Antioquia','Moca','Gris','Menta','Beige','Indu','VINOTINTO','Petroleo',
        'Mandarina','Coral Medio','Bordado','Miel','Lila','Negro/Plata','Negro/Rosa','Negro/Oro','Gris/Plata','Plata','Verde Neon',
        'Dorado'];

    /**cosntantes utilizadas en la creacion del xml de la orden*/
    public static $FUENTE = 'pw';
    public static $CCVENDEDOR = '1037591898';
    public static $USUARIO='ALEXA';
    public static $IVA ='19';

}