<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use \SoapClient;

class CargaArticulos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumo:servicios';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $url = 'http://pyaws.pya.com.co/ServiceSagWeb.svc?singleWsdl';
        $client  =  new SoapClient($url);
        $result = $client->consultaSagJson(['a_s_token'=>'L44rEt98Hj09','a_s_consulta'=>'select * from articulos where sc_tienda_virtual = \'S\'']);
        dd($result);
        //dd($client->__getTypes());

        $this->info('Los articulos se cargaron con exito');
    }
}
