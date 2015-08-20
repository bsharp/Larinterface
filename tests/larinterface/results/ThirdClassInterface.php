<?php

namespace App\Larinterface\Test\Interfaces;

/**
 * Class ThirdClassInterface
 * @package App\Larinterface\Test\Interfaces
 *
 * Generated: 2015-08-20 18:11:31
 */
interface ThirdClassInterface
{
    

    /**
     * Create a new command dispatcher instance.
     *
     * @param  \Illuminate\Contracts\Container\Container  $container
     * @param  \Closure|null  $queueResolver
     * @return void
     */
    public function __construct(Container $container, Closure $queueResolver = null);

    /**
     * Marshal a command and dispatch it to its appropriate handler.
     *
     * @param  mixed  $command
     * @param  array  $array
     * @return mixed
     */
    public function dispatchFromArray($command, array $array);

    /**
     * Marshal a command and dispatch it to its appropriate handler.
     *
     * @param  mixed  $command
     * @param  \ArrayAccess  $source
     * @param  array  $extras
     * @return mixed
     */
    public function dispatchFrom($command, ArrayAccess $source, array $extras = []);

    /**
     * Dispatch a command to its appropriate handler.
     *
     * @param  mixed  $command
     * @param  \Closure|null  $afterResolving
     * @return mixed
     */
    public function dispatch($command, Closure $afterResolving = null);

    /**
     * Dispatch a command to its appropriate handler in the current process.
     *
     * @param  mixed  $command
     * @param  \Closure|null  $afterResolving
     * @return mixed
     */
    public function dispatchNow($command, Closure $afterResolving = null);

    /**
     * Dispatch a command to its appropriate handler behind a queue.
     *
     * @param  mixed  $command
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function dispatchToQueue($command);

    /**
     * Get the handler instance for the given command.
     *
     * @param  mixed  $command
     * @return mixed
     */
    public function resolveHandler($command);

    /**
     * Get the handler class for the given command.
     *
     * @param  mixed  $command
     * @return string
     */
    public function getHandlerClass($command);

    /**
     * Get the handler method for the given command.
     *
     * @param  mixed  $command
     * @return string
     */
    public function getHandlerMethod($command);

    /**
     * Register command-to-handler mappings.
     *
     * @param  array  $commands
     * @return void
     */
    public function maps(array $commands);

    /**
     * Register a fallback mapper callback.
     *
     * @param  \Closure  $mapper
     * @return void
     */
    public function mapUsing(Closure $mapper);

    /**
     * Map the command to a handler within a given root namespace.
     *
     * @param  mixed  $command
     * @param  string  $commandNamespace
     * @param  string  $handlerNamespace
     * @return string
     */
    public static function simpleMapping($command, $commandNamespace, $handlerNamespace);

    /**
     * Set the pipes through which commands should be piped before dispatching.
     *
     * @param  array  $pipes
     * @return $this
     */
    public function pipeThrough(array $pipes);
}
