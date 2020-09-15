<?php
/**
 * Created by PhpStorm.
 * User: DPS-C
 * Date: 10/09/2020
 * Time: 4:44 PM
 */

namespace App\Integracion\Servicios\Rest\Woocommerce;


use GuzzleHttp\Client;

class ClienteServicioWoo
{
    private $baseUrl;
    private $clientRest;
    private $claveClienteWoo;
    private $claveSecretaWoo;


    public function __construct(){
        $this->baseUrl = env('API_ENDPOINT');
        $this->claveClienteWoo = env('CLAVE_CLIENTE');
        $this->claveSecretaWoo = env('CLAVE_SECRETA');
        $this->clientRest =new Client(
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