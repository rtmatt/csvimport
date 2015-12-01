<?php

namespace RTMatt\CSVImport\Providers;

use Illuminate\Support\ServiceProvider;
use RTMatt\CSVImport\Commands\CSVCreateImporterCommand;

class CSVImportServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__ . '/../views', 'csvimport');
        $this->publishes([
            __DIR__ . '/../config/csvimport.php' => config_path('csvimport.php'),
            __DIR__ . '/../Publish'              => app_path()
        ]);

        if(!config('csvimport.custom_route')){
            require __DIR__ . '/../routes/routes.php';
        }

    }


    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {

        $this->mergeConfigFrom(__DIR__ . '/../config/csvimport.php', 'csvimport');

        $this->commands('csvimport:make {importer}');
        $this->app['csvimport:make {importer}'] = $this->app->share(function ($app) {
            return new CSVCreateImporterCommand;
        });

    }



}
