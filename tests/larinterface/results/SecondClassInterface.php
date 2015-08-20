<?php

namespace App\Larinterface\Test\Interfaces;

/**
 * Class SecondClassInterface
 * @package App\Larinterface\Test\Interfaces
 *
 * Generated: 2015-08-20 18:11:31
 */
interface SecondClassInterface
{
    

    /**
     * Create a new encrypter instance.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return void
     */
    public function __construct($key, $cipher = 'AES-128-CBC');

    /**
     * Determine if the given key and cipher combination is valid.
     *
     * @param  string  $key
     * @param  string  $cipher
     * @return bool
     */
    public static function supported($key, $cipher);

    /**
     * Encrypt the given value.
     *
     * @param  string  $value
     * @return string
     */
    public function encrypt($value);

    /**
     * Decrypt the given value.
     *
     * @param  string  $payload
     * @return string
     */
    public function decrypt($payload);
}
