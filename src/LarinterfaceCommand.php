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
        $classes = $this->larinterface->getClasses();

        foreach ($classes as $output => $class_array) {
            foreach ($class_array as $class) {
                $result = $this->larinterface->generate($class, $output);

                $code = is_array($result) ? $result[0] : $result;

                if ($code === Larinterface::SUCCESS) {
                    $msg = '[SUCCESS] ' . $class;

                    if ($result[1] > 0) {
                        $msg .= ' [MISSING: ' . $result[1] . ' comment block]';
                    }

                    $this->info($msg);
                } elseif ($code === Larinterface::EMPTY_CLASS) {
                    $this->comment('No method in class ' . $class . ' to generate an Interface');
                } elseif ($code === Larinterface::NOT_CLASS) {
                    $this->comment('[IGNORED] ' . $class);
                } elseif ($code === Larinterface::NO_MODIFICATION) {
                    // Do nothing
                } else {
                    $this->error('[ERROR]   Can\'t write file: ' . $result[1]);
                }
            }
        }
    }
}
