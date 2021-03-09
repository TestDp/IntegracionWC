<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 10/09/2020
 * Time: 4:44 PM
 */

namespace App\Integracion\Servicios\Rest\Woocommerce;


use GuzzleHttp\Client;

class ServiceClientWoo
{
    private $baseUrl;
    private $clientRest;
    private $claveClienteWoo;
    private $claveSecretaWoo;


  /** public function __construct(){
        $this->baseUrl = env('HOST_DETALLISTAS');
        $this->claveClienteWoo = env('CLAVE_CLIENTE_DETALLISTAS');
        $this->claveSecretaWoo = env('CLAVE_SECRETA_DETALLISTAS');
        $this->clientRest =new Client(
            ['base_uri' => $this->baseUrl ,
                'auth' => [$this->claveClienteWoo, $this->claveSecretaWoo]
            ]);
    }**/
   public function __construct($host,$claveCliente,$claveSecreta){
        $this->baseUrl = $host;
        $this->claveClienteWoo = $claveCliente;
        $this->claveSecretaWoo = $claveSecreta;
        $this->clientRest = new Client(
            ['base_uri' => $this->baseUrl ,
                'auth' => [$this->claveClienteWoo, $this->claveSecretaWoo]
            ]);
    }

    public function  Get($url_api){
        $res = $this->clientRest->get($url_api);
        $result = $res->getBody()->getContents();
        return json_decode($result,TRUE);
    }

    public function Post($url_api,$arrayFormParams){
        $response =  $this->clientRest->post($url_api,['form_params' =>$arrayFormParams]);
        $result = $response->getBody()->getContents();
        return json_decode($result,TRUE);
    }

    public function Put($url_api,$arrayFormParams){
        $response =  $this->clientRest->put($url_api,['form_params' =>$arrayFormParams]);
        $result = $response->getBody()->getContents();
        return json_decode($result,TRUE);
    }

}