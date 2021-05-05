<?php
/**
 * Created by PhpStorm.
 * User: DPS-J
 * Date: 22/10/2020
 * Time: 8:02 PM
 */

namespace App\Console\Commands;

use App\Http\Controllers\OrdenController;
use App\Http\Controllers\ProductoController;
use Illuminate\Console\Command;


class CargaOrdenes  extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'consumo:ordenes';

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
    public $ocontroller;

    public function __construct( OrdenController  $controllerOrden)
    {
        parent::__construct();
        $this->ocontroller = $controllerOrden;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->ocontroller->CrearOrdenesSagDesdeWoo();
        $this->info('Sec crearon las ordenes de la ultima hora ');
    }
}
