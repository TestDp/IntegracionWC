<?php

namespace App\Console;

use App\Integracion\Comunes\Constantes;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\CargaArticulos',
        'App\Console\Commands\CargaOrdenes'

    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('consumo:servicios',[Constantes::$NOMBREHOSTDETALLISTAS])->everyTwoMinutes();
        //$schedule->command('consumo:ordenes')->everyTwoMinutes();
        $schedule->command('consumo:articulos',[Constantes::$NOMBREHOSTDETALLISTAS])->everyFiveMinutes();


        //$schedule->command('consumo:servicios',[Constantes::$NOMBREHOSTMAYORISTAS])->hourly();
        //$schedule->command('consumo:articulos',[Constantes::$NOMBREHOSTMAYORISTAS])->hourly();

        //$schedule->command('consumo:servicios',[Constantes::$NOMBREHOSTDISTRIBUIDORES])->hourly();
        //$schedule->command('consumo:articulos',[Constantes::$NOMBREHOSTDISTRIBUIDORES])->hourly();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
