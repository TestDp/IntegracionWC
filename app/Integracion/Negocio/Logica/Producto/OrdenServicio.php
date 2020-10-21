<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 17/09/2020
 * Time: 8:45 AM
 */

namespace App\Integracion\Negocio\Logica\Producto;


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

    public function  ConsultarOrdenesWoo(){
        $result =  $this->serviceClientWoo->Get('/wp-json/wc/v3/orders');
        return $result;
    }

    public function  ConsultarOrdenWoo($idOrdenWoo){
        $result =  $this->serviceClientWoo->Get('/wp-json/wc/v3/orders/'.$idOrdenWoo);
        return $result;
    }

    public function GuardarOrdenSAG($xmlOrdenSag){
        $result = $this->serviceClientSag->GuardarOrdenSAG($xmlOrdenSag);
        return $result;
    }

    public function CrearXMLOrdenSag(){

        $doc = new  DOMDocument ();

        $doc -> formatOutput = true ;

        $raiz = $doc -> createElement ( "Movimientos" );
        $raiz = $doc -> appendChild ( $raiz );


        $movimiento = $doc -> createElement ( "movimiento" );
        $movimiento = $raiz -> appendChild ( $movimiento );
        $movimiento->setAttribute("movimientoId", "1");
        $movimiento->setAttribute("n_numero_documento", "0");
        $movimiento->setAttribute("num_doc", "606");
        $movimiento->setAttribute("fuente", "OC");
        $movimiento->setAttribute("nit", "1036933852");
        $movimiento->setAttribute("fecha", "2020-10-09");
        $movimiento->setAttribute("d_fecha_documento", "2020-10-09");
        $movimiento->setAttribute("vendedor","1038408871");
        $movimiento->setAttribute("usuario", "SANDRA");
        $movimiento->setAttribute("RealizarCommit", "S");

        //aca iria un ciclo segun las diferentes skus de la orden

        $movimientoDetalle = $doc -> createElement ( "movimientoDetalle" );

        $movimientoDetalle = $movimiento -> appendChild ( $movimientoDetalle );
        $movimientoDetalle->setAttribute("movimientoDetalleId", "1");
        $movimientoDetalle->setAttribute("movimientoId", "1");
        $movimientoDetalle->setAttribute("sc_cual_precio", "1");
        $movimientoDetalle->setAttribute("codigoArticulo", "1300160");
        $movimientoDetalle->setAttribute("cantidad", "9");
        $movimientoDetalle->setAttribute("valorUnitario", "15000");
        $movimientoDetalle->setAttribute("iva", "19");
        $movimientoDetalle->setAttribute("descuento", "0");
        $movimientoDetalle->setAttribute("descuento2", "0");
        $movimientoDetalle->setAttribute("bodega", "63");

        $movimientoDetalle1 = $doc -> createElement ( "movimientoDetalle" );

        $movimientoDetalle1 = $movimiento -> appendChild ( $movimientoDetalle1 );
        //es el concecutivo del item idicado por el ciclo
        $movimientoDetalle1->setAttribute("movimientoDetalleId", "2");
        $movimientoDetalle1->setAttribute("movimientoId", "1");
        $movimientoDetalle1->setAttribute("sc_cual_precio", "1");
        $movimientoDetalle1->setAttribute("codigoArticulo", "1300180");
        $movimientoDetalle1->setAttribute("cantidad", "9");
        $movimientoDetalle1->setAttribute("valorUnitario", "15000");
        $movimientoDetalle1->setAttribute("iva", "19");
        $movimientoDetalle1->setAttribute("descuento", "0");
        $movimientoDetalle1->setAttribute("descuento2", "0");
        $movimientoDetalle1->setAttribute("bodega", "63");

        $movimientosOtrosDatos= $doc -> createElement ( "movimientosOtrosDatos" );

        $movimientosOtrosDatos = $movimiento -> appendChild ( $movimientosOtrosDatos );
        $movimientosOtrosDatos->setAttribute("movimientoId", "1");

/*
        // nro interno woo numerico
        $movimientoId = $doc -> createElement ( "movimientoId" );
        $movimientoId = $movimiento -> appendChild ( $movimientoId );
        $textmovimientoId = $doc -> createTextNode ( 4 );
        $textmovimientoId = $movimientoId -> appendChild ( $textmovimientoId );

        //fecha woo formato 2020/04/30
        $fecha = $doc -> createElement ( "fecha" );
        $fecha = $movimiento -> appendChild ( $fecha );
        $textfecha = $doc -> createTextNode ( "2020/04/30" );
        $textfecha = $fecha -> appendChild ( $textfecha );

        //quemado
        $n_numero_documento = $doc -> createElement ( "n_numero_documento" );
        $n_numero_documento = $movimiento -> appendChild ( $n_numero_documento );
        $textn_numero_documento = $doc -> createTextNode ( 0 );
        $textn_numero_documento = $n_numero_documento -> appendChild ( $textn_numero_documento );


        //quemado como CT . COtizacion o PC : PEDIDOS CLIENTES
        $fuente = $doc -> createElement ( "fuente" );
        $fuente = $movimiento -> appendChild ( $fuente );
        $textfuente = $doc -> createTextNode ( "CT" );
        $textfuente = $fuente -> appendChild ( $textfuente );

        //cedula de cliente
        $nit = $doc -> createElement ( "nit" );
        $nit = $movimiento -> appendChild ( $nit );
        $textnit = $doc -> createTextNode ( 1036933852 );
        $textnit = $nit -> appendChild ( $textnit );


        // ciudad entrega woo, no obligatoria
        $ciudadEntrega = $doc -> createElement ( "ciudadEntrega" );
        $ciudadEntrega = $movimiento -> appendChild ( $ciudadEntrega );
        $textciudadEntrega = $doc -> createTextNode ("CALI");
        $textciudadEntrega = $ciudadEntrega -> appendChild ( $textciudadEntrega );

        //direccion entrega , no obligatoria
        $dieccionEntrega = $doc -> createElement ( "direccionEntrega" );
        $dieccionEntrega = $movimiento -> appendChild ( $dieccionEntrega );
        $textdieccionEntrega = $doc -> createTextNode ( "Calle 25 # 12-29" );
        $textdieccionEntrega = $dieccionEntrega -> appendChild ( $textdieccionEntrega );

        //observaciones entrega , no obligatoria
        $observaciones = $doc -> createElement ( "observaciones" );
        $observaciones = $movimiento -> appendChild ( $observaciones );
        $textobservaciones = $doc -> createTextNode ( "dejar en porteria");
        $textobservaciones = $observaciones -> appendChild ( $textobservaciones );

        //Es obligatorio según el código de fuente Crear vendedor ventas web
        // actualmente este 1038408871 corresponde a Sandra de la ramada
        $vendedor = $doc -> createElement ( "vendedor" );
        $vendedor = $movimiento -> appendChild ( $vendedor );
        $textvendedor = $doc -> createTextNode ( 1038408871 );
        $textvendedor = $vendedor -> appendChild ( $textvendedor );

        //Crear usuario o reutilizar uno, en este caso se utiliza el de SANDRA
        $usuario = $doc -> createElement ( "usuario" );
        $usuario = $movimiento -> appendChild ( $usuario );
        $textusuario = $doc -> createTextNode ( "SANDRA" );
        $textusuario = $usuario -> appendChild ( $textusuario );

        //quemado
        $RealizarCommit = $doc -> createElement ( "RealizarCommit" );
        $RealizarCommit = $movimiento -> appendChild ( $RealizarCommit );
        $textRealizarCommit = $doc -> createTextNode ( "S" );
        $textRealizarCommit = $RealizarCommit -> appendChild ( $textRealizarCommit );

        //aca iria un ciclo segun las diferentes skus de la orden

        $movimientoDetalle = $doc -> createElement ( "movimientoDetalle" );
        $movimientoDetalle = $movimiento -> appendChild ( $movimientoDetalle );

        //es el mismo numero interno woo
        $movimientoId = $doc -> createElement ( "movimientoId" );
        $movimientoId = $movimientoDetalle -> appendChild ( $movimientoId );
        $textmovimientoId = $doc -> createTextNode ( 4 );
        $textmovimientoId = $movimientoId -> appendChild ( $textmovimientoId );

        //es el concecutivo del item idicado por el ciclo
        $movimientoDetalleId = $doc -> createElement ( "movimientoDetalleId" );
        $movimientoDetalleId = $movimientoDetalle -> appendChild ( $movimientoDetalleId );
        $textmovimientoDetalleId = $doc -> createTextNode ( 1 );
        $textmovimientoDetalleId = $movimientoDetalleId -> appendChild ( $textmovimientoDetalleId );

        // quemado
        $sc_cual_precio = $doc -> createElement ( "sc_cual_precio" );
        $sc_cual_precio = $movimientoDetalle -> appendChild ( $sc_cual_precio );
        $textsc_cual_precio = $doc -> createTextNode ( "1" );
        $textsc_cual_precio = $sc_cual_precio -> appendChild ( $textsc_cual_precio );

        // equvalorUnitariolente al sku de woo
        $codigoArticulo = $doc -> createElement ( "codigoArticulo" );
        $codigoArticulo = $movimientoDetalle -> appendChild ( $codigoArticulo );
        $textcodigoArticulo = $doc -> createTextNode ( "1300160" );
        $textcodigoArticulo = $codigoArticulo -> appendChild ( $textcodigoArticulo );

        // cantidad woo
        $cantidad = $doc -> createElement ( "cantidad" );
        $cantidad = $movimientoDetalle -> appendChild ( $cantidad );
        $textcantidad = $doc -> createTextNode ( 6 );
        $textcantidad = $cantidad -> appendChild ( $textcantidad );

        // valor unitario woo
        $valorUnitario = $doc -> createElement ( "valorUnitario" );
        $valorUnitario = $movimientoDetalle -> appendChild ( $valorUnitario );
        $textvalorUnitario = $doc -> createTextNode ( 15000 );
        $textvalorUnitario = $valorUnitario -> appendChild ( $textvalorUnitario );

        //quemado
        $iva = $doc -> createElement ( "iva" );
        $iva = $movimientoDetalle -> appendChild ( $iva );
        $textiva = $doc -> createTextNode ( 19 );
        $textiva = $iva -> appendChild ( $textiva );

        //quemado
        $descuento = $doc -> createElement ( "descuento" );
        $descuento = $movimientoDetalle -> appendChild ( $descuento );
        $textdescuento = $doc -> createTextNode ( 0 );
        $textdescuento = $descuento -> appendChild ( $textdescuento );

        //quemado
        $descuento2 = $doc -> createElement ( "descuento2" );
        $descuento2 = $movimientoDetalle -> appendChild ( $descuento2 );
        $textdescuento2 = $doc -> createTextNode ( 0 );
        $textdescuento2 = $descuento2 -> appendChild ( $textdescuento2 );

        //validar con woo , no obligatoria, formato 2020/06/15
        $fechaEntrega = $doc -> createElement ( "fechaEntrega" );
        $fechaEntrega = $movimientoDetalle -> appendChild ( $fechaEntrega );
        $textfechaEntrega = $doc -> createTextNode ( "2020/06/15" );
        $textfechaEntrega = $fechaEntrega -> appendChild ( $textfechaEntrega );

        //bodega virtual siempre sera 63
        $bodega = $doc -> createElement ( "bodega" );
        $bodega = $movimientoDetalle -> appendChild ( $bodega );
        $textbodega = $doc -> createTextNode ( "67" );
        $textbodega = $bodega -> appendChild ( $textbodega );

*/





        return  $doc->saveXML();


        //echo  'Escrito:' . $doc -> guardar ( "/Usuarios/xcheko51x/Downloads/usuarios.xml" ). 'bytes <br> <br>' ;

    }
}