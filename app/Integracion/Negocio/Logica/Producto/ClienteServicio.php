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

    /*public function InicializarServiceClientWoo($baseUrl, $claveClienteWoo, $claveSecretaWoo){
        $this->clienteServicioWoo->InicializarServiceClientWoo($baseUrl, $claveClienteWoo, $claveSecretaWoo);
    }*/
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
        $textId = $doc -> createTextNode ( "05-VENTA ONLINE" );
        $actividadComercial -> appendChild ( $textId );

        $codigoDaneCiudad = $doc -> createElement ( "codigoDaneCiudad" );
        $codigoDaneCiudad = $cliente -> appendChild ( $codigoDaneCiudad );
        $textcodigoDaneCiudad = $doc -> createTextNode ( "05001" );
        $codigoDaneCiudad -> appendChild ( $textcodigoDaneCiudad );

        $naturaleza = $doc -> createElement ( "naturaleza" );
        $naturaleza = $cliente -> appendChild ( $naturaleza );
        $textnaturaleza = $doc -> createTextNode ( "N" );
        $naturaleza -> appendChild ( $textnaturaleza );

        //campo quemado
        $tipoDocumento = $doc -> createElement ( "tipoDocumento" );
        $tipoDocumento = $cliente -> appendChild ( $tipoDocumento );
        $texttipoDocumento = $doc -> createTextNode ( "C" );
        $tipoDocumento -> appendChild ( $texttipoDocumento );

        $documento = $doc -> createElement ( "documento" );
        $documento = $cliente -> appendChild ( $documento );
        $textdocumento = $doc -> createTextNode ( $clienteWoo->billing->company);
        $documento -> appendChild ( $textdocumento );

        $nombre = $doc -> createElement ( "nombre" );
        $nombre = $cliente -> appendChild ( $nombre );
        $textnombre = $doc -> createTextNode ( strtoupper($clienteWoo->first_name) );
        $nombre -> appendChild ( $textnombre );

        $nombre = $doc -> createElement ( "nombre1" );
        $nombre = $cliente -> appendChild ( $nombre );
        $textnombre = $doc -> createTextNode ( strtoupper($clienteWoo->first_name) );
        $nombre -> appendChild ( $textnombre );

        $nombre = $doc -> createElement ( "apellido1" );
        $nombre = $cliente -> appendChild ( $nombre );
        $textnombre = $doc -> createTextNode ( strtoupper($clienteWoo->last_name) );
        $nombre -> appendChild ( $textnombre );

        $direccion = $doc -> createElement ( "direccion" );
        $direccion = $cliente -> appendChild ( $direccion );
        $textdireccion = $doc -> createTextNode ( $clienteWoo->billing->address_1 );
        $direccion -> appendChild ( $textdireccion );

        //siempre es "N" persona natural
        $tipoTercero = $doc -> createElement ( "tipoTercero" );
        $tipoTercero = $cliente -> appendChild ( $tipoTercero );
        $texttipoTercero = $doc -> createTextNode ( "N" );
        $tipoTercero -> appendChild ( $texttipoTercero );

        $telefonoPpal = $doc -> createElement ( "telefonoPpal" );
        $telefonoPpal = $cliente -> appendChild ( $telefonoPpal );
        $texttelefonoPpal = $doc -> createTextNode ( $clienteWoo->billing->phone );
        $telefonoPpal -> appendChild ( $texttelefonoPpal );

        $email = $doc -> createElement ( "email" );
        $email = $cliente -> appendChild ( $email );
        $textEmail = $doc -> createTextNode ( $clienteWoo->email );
        $email -> appendChild ( $textEmail );

        //siempre es N
        $activoFijo = $doc -> createElement ( "activoFijo" );
        $activoFijo = $cliente -> appendChild ( $activoFijo );
        $textactivoFijo = $doc -> createTextNode ( "N" );
        $activoFijo -> appendChild ( $textactivoFijo );

        // siempre es CONTADO
        $formaPago = $doc -> createElement ( "formaPago" );
        $formaPago = $cliente -> appendChild ( $formaPago );
        $textformaPago = $doc -> createTextNode ( "CONTADO" );
        $formaPago -> appendChild ( $textformaPago );

        // validar zona en SAG   .. siempre ponen marinilla
        $zona = $doc -> createElement ( "zona" );
        $zona = $cliente -> appendChild ( $zona );
        $textzona = $doc -> createTextNode ( "ANTIOQUIA" );
        $zona -> appendChild ( $textzona );

        // siempre es quemado en N por ser siempre persona natural
        $retenedor = $doc -> createElement ( "retenedor" );
        $retenedor = $cliente -> appendChild ( $retenedor );
        $textretenedor = $doc -> createTextNode ( "N" );
        $retenedor -> appendChild ( $textretenedor );

        // siempre es "S"
        $iva = $doc -> createElement ( "iva" );
        $iva = $cliente -> appendChild ( $iva );
        $textiva = $doc -> createTextNode ( "S" );
        $iva -> appendChild ( $textiva );

        //siempre es "S"
        $activoComercial = $doc -> createElement ( "activoComercial" );
        $activoComercial = $cliente -> appendChild ( $activoComercial );
        $textactivoComercial = $doc -> createTextNode ( "S" );
        $activoComercial -> appendChild ( $textactivoComercial );

        //siempre es "S"
        $activo = $doc -> createElement ( "activo" );
        $activo = $cliente -> appendChild ( $activo );
        $textactivo = $doc -> createTextNode ( "S" );
        $activo -> appendChild ( $textactivo );

        //siempre es MINORISTA por ser persona natural
        $tipoCliente = $doc -> createElement ( "tipoCliente" );
        $tipoCliente = $cliente -> appendChild ( $tipoCliente );
        $texttipoCliente = $doc -> createTextNode ( "V" );
        $tipoCliente -> appendChild ( $texttipoCliente );

        //siempre es 0 numeric
        $comisionVentas = $doc -> createElement ( "comisionVentas" );
        $comisionVentas = $cliente -> appendChild ( $comisionVentas );
        $textcomisionVentas = $doc -> createTextNode ( 0 );
        $comisionVentas -> appendChild ( $textcomisionVentas );

        //siempre es 0 numeric
        $comisionCobros = $doc -> createElement ( "comisionCobros" );
        $comisionCobros = $cliente -> appendChild ( $comisionCobros );
        $textcomisionCobros = $doc -> createTextNode ( 0 );
        $comisionCobros -> appendChild ( $textcomisionCobros );

        //siempre es 0 numeric
        $descuento = $doc -> createElement ( "descuento" );
        $descuento = $cliente -> appendChild ( $descuento );
        $textdescuento = $doc -> createTextNode ( 0 );
        $descuento -> appendChild ( $textdescuento );

        //siempre es 0 numeric
        $descuentoPp = $doc -> createElement ( "descuentoPp" );
        $descuentoPp = $cliente -> appendChild ( $descuentoPp );
        $textdescuentoPp = $doc -> createTextNode ( 0 );
        $descuentoPp -> appendChild ( $textdescuentoPp );

        //siempre es 1 segun las reglas de negocio de la ramada precio 1 string
        $precioVenta = $doc -> createElement ( "precioVenta" );
        $precioVenta = $cliente -> appendChild ( $precioVenta );
        $textprecioVenta = $doc -> createTextNode ( "4" );
        $precioVenta -> appendChild ( $textprecioVenta );

        //siempre es 999999999 numerico
        $cupoMaximo = $doc -> createElement ( "cupoMaximo" );
        $cupoMaximo = $cliente -> appendChild ( $cupoMaximo );
        $textcupoMaximo = $doc -> createTextNode ( 99999999999 );
        $cupoMaximo -> appendChild ( $textcupoMaximo );

        $codigoPostal = $doc -> createElement ( "codigoPostal" );
        $codigoPostal = $cliente -> appendChild ( $codigoPostal );
        $textcodigoPostal = $doc -> createTextNode ( '000000');
        $codigoPostal -> appendChild ( $textcodigoPostal );

        $habeasData = $doc -> createElement ( "habeasData" );
        $habeasData = $cliente -> appendChild ( $habeasData );
        $texthabeasData = $doc -> createTextNode ( 'S');
        $habeasData -> appendChild ( $texthabeasData );

        $centroCosto = $doc -> createElement ( "centroCosto" );
        $centroCosto = $cliente -> appendChild ( $centroCosto );
        $textcentroCosto = $doc -> createTextNode ( '1024');
        $centroCosto -> appendChild ( $textcentroCosto );

        $generaFacElectronica = $doc -> createElement ( "generafacelectronica" );
        $generaFacElectronica = $cliente -> appendChild ( $generaFacElectronica );
        $textgeneraFacElectronica = $doc -> createTextNode ( 'S');
        $generaFacElectronica -> appendChild ( $textgeneraFacElectronica );

        $emailFacElectronica = $doc -> createElement ( "emailFacElectronica" );
        $emailFacElectronica = $cliente -> appendChild ( $emailFacElectronica );
        $textemailFacElectronica = $doc -> createTextNode ( $clienteWoo->email);
        $emailFacElectronica -> appendChild ( $textemailFacElectronica );

        $responsabilidadFiscal = $doc -> createElement ( "responsabilidadFiscal" );
        $responsabilidadFiscal = $cliente -> appendChild ( $responsabilidadFiscal );
        $textresponsabilidadFiscal = $doc -> createTextNode ( 'R-99-PN');
        $responsabilidadFiscal -> appendChild ( $textresponsabilidadFiscal );

        return  $doc->saveXML();

    }
}