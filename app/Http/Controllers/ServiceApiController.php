<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use \SoapClient;


class ServiceApiController extends Controller
{

    public $baseUrl;
    public  $clientRest;

    public function __construct(){
        $this->baseUrl = env('API_ENDPOINT');
        $this->clientRest =new Client(
            ['base_uri' => $this->baseUrl ,
                'auth' => ['ck_6430e3183b00f310c55cbd480c7f88107a128003', 'cs_80387c7dba778dbaa799e47e8ec7aecdd75d872f']
            ]);
    }

    public function  Get($url_api){
        $res = $this->clientRest->get($url_api);
        $result = $res->getBody()->getContents();
        return json_decode($result,TRUE);
    }
  public function GetProducts(){

      $baseUrl = env('API_ENDPOINT');
      $client = new Client(
          ['base_uri' => $baseUrl,
          'auth' => ['ck_6430e3183b00f310c55cbd480c7f88107a128003', 'cs_80387c7dba778dbaa799e47e8ec7aecdd75d872f']
      ]);
      $res = $client->get('/wp-json/wc/v3/products');
      $result = $res->getBody()->getContents();
      $arr = json_decode($result,TRUE);
      dd($arr);
  }


    public function GetProductPpost(){

        $baseUrl = env('API_ENDPOINT');
        $client = new Client(
            ['base_uri' => $baseUrl,
                'auth' => ['ck_6430e3183b00f310c55cbd480c7f88107a128003', 'cs_80387c7dba778dbaa799e47e8ec7aecdd75d872f']
            ]);
        $response = $client->post('/wp-json/wc/v3/products', [
            'form_params' => [
                'name' => 'Premium Quality',
                'type' => 'simple',
                'regular_price' => '21.99',
                'description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas. Vestibulum tortor quam, feugiat vitae, ultricies eget, tempor sit amet, ante. Donec eu libero sit amet quam egestas semper. Aenean ultricies mi vitae est. Mauris placerat eleifend leo.',
                'short_description' => 'Pellentesque habitant morbi tristique senectus et netus et malesuada fames ac turpis egestas.',
                'categories' => [
                    [
                        'id' => 15
                    ]
                ],
                'images' => [
                    [
                        'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_front.jpg'
                    ],
                    [
                        'src' => 'http://demo.woothemes.com/woocommerce/wp-content/uploads/sites/56/2013/06/T_2_back.jpg'
                    ]
                ]
            ]
        ]);
    }



    public function GetProduct(){
      $url = 'http://pyaws.pya.com.co/ServiceSagWeb.svc?singleWsdl';
      $client  =  new SoapClient($url);
      $result = $client->consultaSagJson(['a_s_token'=>'L44rEt98Hj09','a_s_consulta'=>'select * from articulos where sc_tienda_virtual = \'S\'']);
      dd($result);
      //dd($client->__getTypes());
    }

}
