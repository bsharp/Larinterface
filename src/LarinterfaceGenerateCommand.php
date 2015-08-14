<?php

namespace Bsharp\Larinterface;

use Illuminate\Console\Command;

/**
 * Class LarinterfaceGenerateCommand
 * @package Bsharp\Larinterface
 */
class LarinterfaceGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'larinterface:generate';

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

        $this->larinterface->store();

        foreach ($classes as $class => $output) {

            $args = [
                $class,
                $output['output'],
                $output['output_file'],
                $output['input_file'],
                $output['namespace'],
                $output['name']
            ];

            // Just in case
            $cli_args = '';
            foreach ($args as $arg) {
                $cli_args .= '"' . $arg . '" ';
            }

            $handle = popen('php artisan larinterface:encapsulate ' . $cli_args . ' 2>&1', 'r');

            $result = fread($handle, 2096);
            $result = explode("\n", $result);

            array_pop($result);

            pclose($handle);

            if (count($result) === 1) {
                $result = $result[0];
            } elseif (str_contains(strtolower($result[0]), 'php parse error')) { // In case of PHP parse error
                $result = Larinterface::PARSE_ERROR;
            }

            $code = is_array($result) ? (int)$result[0] : (int)$result;

            if ($code === Larinterface::SUCCESS) {
                $msg = '[SUCCESS]     ' . $class;

                if ((int)$result[1] > 0) {
                    $msg .= ' [MISSING: ' . $result[1] . ' comment block]';
                }

                $this->info($msg);
            } elseif ($code === Larinterface::EMPTY_CLASS) {
                $this->comment('No method in class ' . $class . ' to generate an Interface');
            } elseif ($code === Larinterface::NOT_CLASS) {
                $this->comment('[IGNORED]     ' . $class);
            } elseif ($code === Larinterface::NO_MODIFICATION) {
                $this->info('[UP TO DATE]  ' . $class);
            } elseif ($code === Larinterface::PARSE_ERROR) {
                $this->error('[PARSE ERROR] ' . $class);
            } else {
                $this->error('[ERROR]       Can\'t write file: ' . $result[1]);
            }
        }
    }
}
