<?php
/**
 * Created by PhpStorm.
 * User: DPS-J
 * Date: 21/09/2020
 * Time: 8:19 PM
 */

namespace App\Integracion\Negocio\Logica\Producto;

use App\Integracion\Comunes\Constantes;
use App\Integracion\Servicios\Rest\Woocommerce\ServiceClientWoo;
use App\Integracion\Servicios\Soap\Sag\ServiceClientSag;
use DOMDocument;

class ClienteServicio
{

    public $clienteServicioWoo;
    public $clienteServicioSag;

    public function __construct(ServiceClientWoo $clienteServicioWoo, ServiceClientSag $clienteServicioSag){
        $this->clienteServicioWoo = $clienteServicioWoo;
        $this->clienteServicioSag = $clienteServicioSag;
    }

    public  function  ConsultarClientesWoo(){
       $result =  $this->clienteServicioWoo->Get(Constantes::$URLBASE.Constantes::$ENDPOINTCLIENTES);
        return $result;
    }

    public  function  ConsultarClienteWoo($idClienteWoo){
        $result =  $this->clienteServicioWoo->Get(Constantes::$URLBASE.Constantes::$ENDPOINTCLIENTES.'/'.$idClienteWoo);
        return $result;
    }


    public function GuardarClientesSAG($xmlClienteSag){
        $result = $this->clienteServicioSag ->GuardarClientesSag($xmlClienteSag);
        return $result;
    }


    public function CrearXMLClienteSag($clienteWoo){

        $doc = new  DOMDocument ();

        $doc -> formatOutput = true ;

        $raiz = $doc -> createElement ( "clientes" );
        $raiz = $doc -> appendChild ( $raiz );

        $cliente = $doc -> createElement ( "cliente" );
        $cliente = $raiz -> appendChild ( $cliente );

        $actividadComercial = $doc -> createElement ( "actividadComercial" );
        $actividadComercial = $cliente -> appendChild ( $actividadComercial );
        $textId = $doc -> createTextNode ( "COMERCIANTE" );
        $textId = $actividadComercial -> appendChild ( $textId );

        $codigoDaneCiudad = $doc -> createElement ( "codigoDaneCiudad" );
        $codigoDaneCiudad = $cliente -> appendChild ( $codigoDaneCiudad );
        $textcodigoDaneCiudad = $doc -> createTextNode ( "05440" );
        $textcodigoDaneCiudad = $codigoDaneCiudad -> appendChild ( $textcodigoDaneCiudad );

        $naturaleza = $doc -> createElement ( "naturaleza" );
        $naturaleza = $cliente -> appendChild ( $naturaleza );
        $textnaturaleza = $doc -> createTextNode ( "N" );
        $textnaturaleza = $naturaleza -> appendChild ( $textnaturaleza );

        //campo quemado
        $tipoDocumento = $doc -> createElement ( "tipoDocumento" );
        $tipoDocumento = $cliente -> appendChild ( $tipoDocumento );
        $texttipoDocumento = $doc -> createTextNode ( "C" );
        $texttipoDocumento = $tipoDocumento -> appendChild ( $texttipoDocumento );

        $documento = $doc -> createElement ( "documento" );
        $documento = $cliente -> appendChild ( $documento );
        $textdocumento = $doc -> createTextNode ( $clienteWoo->billing->phone);//despues de que se pida la cc de la woo se cambia por company
        $textdocumento = $documento -> appendChild ( $textdocumento );

        $nombre = $doc -> createElement ( "nombre" );
        $nombre = $cliente -> appendChild ( $nombre );
        $textnombre = $doc -> createTextNode ( $clienteWoo->first_name ." ".$clienteWoo->last_name );
        $textnombre = $nombre -> appendChild ( $textnombre );

        $direccion = $doc -> createElement ( "direccion" );
        $direccion = $cliente -> appendChild ( $direccion );
        $textdireccion = $doc -> createTextNode ( $clienteWoo->billing->address_1 );
        $textdireccion = $direccion -> appendChild ( $textdireccion );

        //siempre es "N" persona natural
        $tipoTercero = $doc -> createElement ( "tipoTercero" );
        $tipoTercero = $cliente -> appendChild ( $tipoTercero );
        $texttipoTercero = $doc -> createTextNode ( "N" );
        $texttipoTercero = $tipoTercero -> appendChild ( $texttipoTercero );

        $telefonoPpal = $doc -> createElement ( "telefonoPpal" );
        $telefonoPpal = $cliente -> appendChild ( $telefonoPpal );
        $texttelefonoPpal = $doc -> createTextNode ( $clienteWoo->billing->phone );
        $texttelefonoPpal = $telefonoPpal -> appendChild ( $texttelefonoPpal );

        $email = $doc -> createElement ( "email" );
        $email = $cliente -> appendChild ( $email );
        $textEmail = $doc -> createTextNode ( $clienteWoo->email );
        $textEmail = $email -> appendChild ( $textEmail );

        //siempre es N
        $activoFijo = $doc -> createElement ( "activoFijo" );
        $activoFijo = $cliente -> appendChild ( $activoFijo );
        $textactivoFijo = $doc -> createTextNode ( "N" );
        $textactivoFijo = $activoFijo -> appendChild ( $textactivoFijo );

        // siempre es CONTADO
        $formaPago = $doc -> createElement ( "formaPago" );
        $formaPago = $cliente -> appendChild ( $formaPago );
        $textformaPago = $doc -> createTextNode ( "CONTADO" );
        $textformaPago = $formaPago -> appendChild ( $textformaPago );

        // validar zona en SAG   .. siempre ponen marinilla
        $zona = $doc -> createElement ( "zona" );
        $zona = $cliente -> appendChild ( $zona );
        $textzona = $doc -> createTextNode ( "ANTIOQUIA" );
        $textzona = $zona -> appendChild ( $textzona );

        // siempre es quemado en N por ser siempre persona natural
        $retenedor = $doc -> createElement ( "retenedor" );
        $retenedor = $cliente -> appendChild ( $retenedor );
        $textretenedor = $doc -> createTextNode ( "N" );
        $textretenedor = $retenedor -> appendChild ( $textretenedor );

        // siempre es "S"
        $iva = $doc -> createElement ( "iva" );
        $iva = $cliente -> appendChild ( $iva );
        $textiva = $doc -> createTextNode ( "S" );
        $textiva = $iva -> appendChild ( $textiva );

        //siempre es "S"
        $activoComercial = $doc -> createElement ( "activoComercial" );
        $activoComercial = $cliente -> appendChild ( $activoComercial );
        $textactivoComercial = $doc -> createTextNode ( "S" );
        $textactivoComercial = $activoComercial -> appendChild ( $textactivoComercial );

        //siempre es "S"
        $activo = $doc -> createElement ( "activo" );
        $activo = $cliente -> appendChild ( $activo );
        $textactivo = $doc -> createTextNode ( "S" );
        $textactivo = $activo -> appendChild ( $textactivo );

        //siempre es MINORISTA por ser persona natural
        $tipoCliente = $doc -> createElement ( "tipoCliente" );
        $tipoCliente = $cliente -> appendChild ( $tipoCliente );
        $texttipoCliente = $doc -> createTextNode ( "OTROS" );
        $texttipoCliente = $tipoCliente -> appendChild ( $texttipoCliente );

        //siempre es 0 numeric
        $comisionVentas = $doc -> createElement ( "comisionVentas" );
        $comisionVentas = $cliente -> appendChild ( $comisionVentas );
        $textcomisionVentas = $doc -> createTextNode ( 0 );
        $textcomisionVentas = $comisionVentas -> appendChild ( $textcomisionVentas );

        //siempre es 0 numeric
        $comisionCobros = $doc -> createElement ( "comisionCobros" );
        $comisionCobros = $cliente -> appendChild ( $comisionCobros );
        $textcomisionCobros = $doc -> createTextNode ( 0 );
        $textcomisionCobros = $comisionCobros -> appendChild ( $textcomisionCobros );

        //siempre es 0 numeric
        $descuento = $doc -> createElement ( "descuento" );
        $descuento = $cliente -> appendChild ( $descuento );
        $textdescuento = $doc -> createTextNode ( 0 );
        $textdescuento = $descuento -> appendChild ( $textdescuento );

        //siempre es 0 numeric
        $descuentoPp = $doc -> createElement ( "descuentoPp" );
        $descuentoPp = $cliente -> appendChild ( $descuentoPp );
        $textdescuentoPp = $doc -> createTextNode ( 0 );
        $textdescuentoPp = $descuentoPp -> appendChild ( $textdescuentoPp );

        //siempre es 1 segun las reglas de negocio de la ramada precio 1 string
        $precioVenta = $doc -> createElement ( "precioVenta" );
        $precioVenta = $cliente -> appendChild ( $precioVenta );
        $textprecioVenta = $doc -> createTextNode ( "1" );
        $textprecioVenta = $precioVenta -> appendChild ( $textprecioVenta );

        //siempre es 999999999 numerico
        $cupoMaximo = $doc -> createElement ( "cupoMaximo" );
        $cupoMaximo = $cliente -> appendChild ( $cupoMaximo );
        $textcupoMaximo = $doc -> createTextNode ( 99999999999 );
        $textcupoMaximo = $cupoMaximo -> appendChild ( $textcupoMaximo );

        $Apellido1 = $doc -> createElement ( "Apellido1" );
        $Apellido1 = $cliente -> appendChild ( $Apellido1 );
        $textApellido1 = $doc -> createTextNode ( $clienteWoo->last_name );
        $textApellido1 = $Apellido1 -> appendChild ( $textApellido1 );


        $Nombre1 = $doc -> createElement ( "Nombre1" );
        $Nombre1 = $cliente -> appendChild ( $Nombre1 );
        $textNombre1 = $doc -> createTextNode ( $clienteWoo->first_name );
        $textNombre1 = $Nombre1 -> appendChild ( $textNombre1 );

       return  $doc->saveXML();

    }
}