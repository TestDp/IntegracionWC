<?php

namespace App\Console\Commands;

use App\Http\Controllers\ProductoController;
use App\Integracion\Comunes\Constantes;
use Illuminate\Console\Command;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use \SoapClient;
use  App\Http\Controllers;

class CargaArticulos extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumo:servicios {host}';

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
    public $pcontroller;
    public function __construct( ProductoController  $controllerProducto)
    {
         parent::__construct();
        $this->pcontroller = $controllerProducto;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
       $nombreHost = $this->argument('host');
       switch ($nombreHost){
           case Constantes::$NOMBREHOSTDETALLISTAS:
                   $baseUrl = env('HOST_DETALLISTAS');
                   $claveClienteWoo = env('CLAVE_CLIENTE_DETALLISTAS');
                   $claveSecretaWoo = env('CLAVE_SECRETA_DETALLISTAS');
                   $this->pcontroller->InicializarServiceClientWoo($baseUrl,$claveClienteWoo,$claveSecretaWoo);
                   $this->pcontroller->ActualizarInventarioProductosWoo(Constantes::$PRECIODETALLISTAS);
               break;
           case Constantes::$NOMBREHOSTMAYORISTAS:
                   $baseUrl = env('HOST_MAYORISTAS');
                   $claveClienteWoo = env('CLAVE_CLIENTE_MAYORISTAS');
                   $claveSecretaWoo = env('CLAVE_SECRETA_MAYORISTAS');
                   $this->pcontroller->InicializarServiceClientWoo($baseUrl,$claveClienteWoo,$claveSecretaWoo);
                   $this->pcontroller->ActualizarInventarioProductosWoo(Constantes::$PRECIOMAYORISTAS);
               break;
           case Constantes::$NOMBREHOSTDISTRIBUIDORES:
                   $baseUrl = env('HOST_DISTRIBUIDORES');
                   $claveClienteWoo = env('CLAVE_CLIENTE_DISTRIBUIDORES');
                   $claveSecretaWoo = env('CLAVE_SECRETA_DISTRIBUIDORES');
                   $this->pcontroller->InicializarServiceClientWoo($baseUrl,$claveClienteWoo,$claveSecretaWoo);
                   $this->pcontroller->ActualizarInventarioProductosWoo(Constantes::$PRECIODISTRIBUIDORES);
               break;
           default:
               $this->info('comando no valido');
       }
        $this->info('Se Actualizo el inventario  con exito');

    }
}
