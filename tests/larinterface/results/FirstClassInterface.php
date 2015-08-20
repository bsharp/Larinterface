<?php

namespace App\Larinterface\Test\Interfaces;

/**
 * Class FirstClassInterface
 * @package App\Larinterface\Test\Interfaces
 *
 * Generated: 2015-08-20 18:11:31
 */
interface FirstClassInterface
{
    /**
     * Create a new Artisan console application.
     *
     * @param  \Illuminate\Contracts\Container\Container $laravel
     * @param  \Illuminate\Contracts\Events\Dispatcher $events
     * @param  string $version
     */
    public function __construct(Container $laravel, Dispatcher $events, $version);

    /**
     * Run an Artisan console command by name.
     *
     * @param  string  $command
     * @param  array  $parameters
     * @return int
     */
    public function call($command, array $parameters = []);

    /**
     * Get the output for the last run command.
     *
     * @return string
     */
    public function output();

    /**
     * Add a command to the console.
     *
     * @param  \Symfony\Component\Console\Command\Command  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function add(SymfonyCommand $command);

    /**
     * Add a command, resolving through the application.
     *
     * @param  string  $command
     * @return \Symfony\Component\Console\Command\Command
     */
    public function resolve($command);

    /**
     * Resolve an array of commands through the application.
     *
     * @param  array|mixed  $commands
     * @return $this
     */
    public function resolveCommands($commands);

    /**
     * Get the Laravel application instance.
     *
     * @return \Illuminate\Contracts\Foundation\Application
     */
    public function getLaravel();
}
