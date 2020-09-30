<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 10/09/2020
 * Time: 4:56 PM
 */

namespace App\Integracion\Servicios\Soap\Sag;


use SoapClient;

class ServiceClientSag
{
    private $clientSoap;
    private $baseUrlSag;
    private $tokenSag;

    public function __construct(){
        $this->baseUrlSag = env('API_ENDPOINT_SAG');
        $this->tokenSag = env('TOKEN_SAG');
        $this->clientSoap = new SoapClient($this->baseUrlSag);
    }

    public function GetConsultaSagJson($query){
        $result = $this->clientSoap->consultaSagJson(['a_s_token'=> $this->tokenSag ,'a_s_consulta' => $query]);
        return json_decode($result->consultaSagJsonResult);
    }

    public function GuardarClientesSag($xml){
        $result = $this->clientSoap->insercionSag(['a_s_token'=> $this->tokenSag ,'a_s_tipo' => 1, 'a_s_xml' => $xml]);
        return json_decode($result->consultaSagJsonResult);
    }
}