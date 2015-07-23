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
        $this->commands([LarinterfaceCommand::class]);
    }
}
