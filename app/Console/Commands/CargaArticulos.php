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
        $this->pcontroller->ActualizarInventarioProductosWoo();
        $this->info('Se Actualizo el inventario  con exito');
    }
}
