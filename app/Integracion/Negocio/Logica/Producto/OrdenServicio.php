<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 17/09/2020
 * Time: 8:45 AM
 */

namespace App\Integracion\Negocio\Logica\Producto;


use App\Integracion\Comunes\Constantes;
use App\Integracion\Servicios\Rest\Woocommerce\ServiceClientWoo;
use App\Integracion\Servicios\Soap\Sag\ServiceClientSag;
use DOMDocument;

class OrdenServicio
{

    public $serviceClientWoo;
    public $serviceClientSag;

    public function __construct(ServiceClientWoo $serviceClientWoo, ServiceClientSag $serviceClientSag){
        $this->serviceClientWoo = $serviceClientWoo;
        $this->serviceClientSag = $serviceClientSag;
    }

    public function InicializarServiceClientWoo($baseUrl, $claveClienteWoo, $claveSecretaWoo){
        $this->serviceClientWoo->InicializarServiceClientWoo($baseUrl, $claveClienteWoo, $claveSecretaWoo);
    }
    public function  ConsultarOrdenesWoo(){
        $result =  $this->serviceClientWoo->Get(Constantes::$URLBASE.Constantes::$ENDPOINTORDENES);
        return $result;
    }
    public function  ConsultarOrdenesWooByDate($fecha){
        $result =  $this->serviceClientWoo->Get(Constantes::$URLBASE.Constantes::$ENDPOINTORDENES.'.?after='.$fecha);
        return $result;
    }

    public function  ConsultarOrdenWoo($idOrdenWoo){
        $result =  $this->serviceClientWoo->Get(Constantes::$URLBASE.Constantes::$ENDPOINTORDENES.'/'.$idOrdenWoo);
        return $result;
    }

    public function GuardarOrdenSAG($xmlOrdenSag){
        $result = $this->serviceClientSag->GuardarOrdenSAG($xmlOrdenSag);
        return $result;
    }

    public function CrearXMLOrdenSag($ordenWoo,$clienteWoo){
        $fechas = explode("T", $ordenWoo->date_created);
        $doc = new  DOMDocument ();
        $doc -> formatOutput = true ;
        $raiz = $doc -> createElement ( "Movimientos" );
        $raiz = $doc -> appendChild ( $raiz );
        $movimiento = $doc -> createElement ( "movimiento" );
        $movimiento = $raiz -> appendChild ( $movimiento );
        $movimiento->setAttribute("movimientoId", 1);
        $movimiento->setAttribute("n_numero_documento", "0");//se envia cero
        $movimiento->setAttribute("num_doc", $ordenWoo->id);
        $movimiento->setAttribute("fuente", "PW");//PD : PEDIDOS MEDELLIN temporal mientras que se crea PW
        $movimiento->setAttribute("nit", $clienteWoo->s_identificador);
        $movimiento->setAttribute("fecha", $fechas[0]);
        $movimiento->setAttribute("d_fecha_documento", $fechas[0]);
        $movimiento->setAttribute("vendedor","222222228");
        $movimiento->setAttribute("usuario", "VENTAS POR INTERNET");
        $movimiento->setAttribute("RealizarCommit", "S");
        //aca iria un ciclo segun las diferentes skus de la orden
        $ind =1;
        foreach ($ordenWoo->line_items as $detalleProducto)
        {
            $detalleProducto = (object)$detalleProducto;
            $movimientoDetalle = $doc->createElement("movimientoDetalle");
            $movimientoDetalle = $movimiento->appendChild($movimientoDetalle);
            $movimientoDetalle->setAttribute("movimientoDetalleId", $ind);
            $movimientoDetalle->setAttribute("movimientoId", 1);
            $movimientoDetalle->setAttribute("sc_cual_precio", 1);
            $movimientoDetalle->setAttribute("codigoArticulo", $detalleProducto->sku);
            $movimientoDetalle->setAttribute("cantidad", $detalleProducto->quantity);
            $movimientoDetalle->setAttribute("valorUnitario",$detalleProducto->price);
            $movimientoDetalle->setAttribute("iva", "19");
            $movimientoDetalle->setAttribute("descuento", "0");
            $movimientoDetalle->setAttribute("descuento2", "0");
            $movimientoDetalle->setAttribute("bodega", "RN1");
            $ind = $ind + 1;
        }
        $movimientosOtrosDatos= $doc -> createElement ( "movimientosOtrosDatos" );
        $movimientosOtrosDatos = $movimiento -> appendChild ( $movimientosOtrosDatos );
        $movimientosOtrosDatos->setAttribute("movimientoId",  1);
        return  $doc->saveXML();
    }

    public function ValidarOrdenRepetidaSag($idDocumentoWoo , $date){
        $result = $this->serviceClientSag->
        GetConsultaSagJson("select  ka_nl_tercero,d_fecha_documento, ss_nro_dcto
                            from movimientos
                            where ss_nro_dcto =  '".$idDocumentoWoo."' and d_fecha_documento = '".$date."'");
        return $result;
    }
}