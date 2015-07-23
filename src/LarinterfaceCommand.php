<?php

namespace Bsharp\Larinterface;

use Illuminate\Console\Command;

/**
 * Class LarinterfaceCommand
 * @package Bsharp\Larinterface
 */
class LarinterfaceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larinterface:make';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Interface from PHP classes.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('Hello World!');
    }
}
