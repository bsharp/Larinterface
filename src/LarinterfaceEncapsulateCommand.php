<?php

namespace Bsharp\Larinterface;

use Illuminate\Console\Command;

/**
 * Class LarinterfaceEncapsulateCommand
 * @package Bsharp\Larinterface
 */
class LarinterfaceEncapsulateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larinterface:encapsulate {class} {output} {output_file} {input_file} {namespace} {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command launched as a sub process by larinterface:generate.';

    /**
     * @var Larinterface
     */
    private $larinterface;

    /**
     * Create a new command instance.
     *
     * @param Larinterface $larinterface
     */
    public function __construct(Larinterface $larinterface)
    {
        parent::__construct();

        $this->larinterface = $larinterface;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $return = $this->larinterface->generate(
            $this->argument('class'),
            $this->argument('output'),
            $this->argument('output_file'),
            $this->argument('input_file'),
            $this->argument('namespace'),
            $this->argument('name')
        );

        if (!is_array($return)) {
            $return = ['code' => $return];
        }

        echo json_encode($return);
    }
}
