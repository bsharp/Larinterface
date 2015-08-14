<?php namespace Bsharp\Larinterface;

use Illuminate\Support\ServiceProvider;

/**
 * Class LarinterfaceServiceProvider
 * @package Bsharp\Larinterface
 */
class LarinterfaceServiceProvider extends ServiceProvider
{
    protected $defer = false;

    public function register()
    {
        // Register publish config
        $this->publishes([
            __DIR__ . '/../publish/config/larinterface.php' => config_path('larinterface.php')
        ], 'config');

        // Register command
        $this->commands([LarinterfaceGenerateCommand::class]);
        $this->commands([LarinterfaceEncapsulateCommand::class]);
    }
}
