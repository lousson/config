<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 textwidth=75: *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Copyright (c) 2012 - 2013, The Lousson Project                        *
 *                                                                       *
 * All rights reserved.                                                  *
 *                                                                       *
 * Redistribution and use in source and binary forms, with or without    *
 * modification, are permitted provided that the following conditions    *
 * are met:                                                              *
 *                                                                       *
 * 1) Redistributions of source code must retain the above copyright     *
 *    notice, this list of conditions and the following disclaimer.      *
 * 2) Redistributions in binary form must reproduce the above copyright  *
 *    notice, this list of conditions and the following disclaimer in    *
 *    the documentation and/or other materials provided with the         *
 *    distribution.                                                      *
 *                                                                       *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS   *
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT     *
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS     *
 * FOR A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE        *
 * COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,            *
 * INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES    *
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)    *
 * HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT,   *
 * STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)         *
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED   *
 * OF THE POSSIBILITY OF SUCH DAMAGE.                                    *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * */

/**
 *  Lousson\Config\GenericConfig class definition
 *
 *  @package    org.lousson.config
 *  @copyright  (c) 2012 - 2013, The Lousson Project
 *  @license    http://opensource.org/licenses/bsd-license.php New BSD License
 *  @author     Mathias J. Hennig <mhennig at quirkies.org>
 *  @filesource
 */
namespace Lousson\Config\Generic;

/** Interfaces: */
use Lousson\Config\AnyConfigEntity;
/** Dependencies: */
use Lousson\Config\AbstractConfig;
/** Exceptions: */
use Lousson\Config\Error\ConfigArgumentError;
use Lousson\Config\Error\ConfigRuntimeError;

/**
 *  A generic implementation of the AnyConfig interface
 *
 *  The Lousson\Config\Generic\GenericConfig class implements the AnyConfig
 *  interface based on a setter method for single configuration options.
 *
 *  @since      lousson/Lousson_Config-0.2.0
 *  @package    org.lousson.config
 */
class GenericConfig
    extends AbstractConfig
    implements AnyConfigEntity
{
    /**
     *  Update the value of a particular option
     *
     *  The setOption() method is used to assign the given $value to the
     *  option identified by the given $key.
     *
     *  @param  string              $name           The option name
     *  @param  mixed               $value          The option value
     *
     *  @throws \Lousson\Config\AnyConfigException
     *          Raised in case the $name or $value is malformed
     */
    public function setOption($name, $value)
    {
        try {
            $name = $this->normalizeName($name, "update");
            $value = $this->normalizeValue($value, "update");
        }
        catch (\Lousson\Config\Error\ConfigRuntimeError $error) {
            $message = $error->getMessage();
            throw new ConfigArgumentError($message);
        }

        $this->options[$name] = $value;
    }

    /**
     *  Delete the value of a particular option
     *
     *  The delOption() method is used to remove the given option
     *  identified by the given $name.
     *
     *  @param  string              $name           The option name
     *
     *  @throws \Lousson\Config\AnyConfigException
     *          Raised in case the $name is malformed
     */
    public function delOption($name) {
        try {
            $name = $this->normalizeName($name, "delete");
        }
        catch (\Lousson\Config\Error\ConfigRuntimeError $error) {
            $message = $error->getMessage();
            throw new ConfigArgumentError($message);
        }

        unset($this->options[$name]);
    }

    /**
     *  Obtain the value of a particular option
     *
     *  The getOption() method will return the value associated with the
     *  option identified by the given $name. If there is no such option,
     *  it will either return the $fallback value - if provided -, or
     *  raise an exception.
     *
     *  @param  string              $name           The option name
     *  @param  mixed               $fallback       The fallback value
     *
     *  @return mixed
     *          The value of the option is returned on success
     *
     *  @throws \Lousson\Config\AnyConfigException
     *          Raised in case of any error
     *
     *  @link   http://php.net/manual/en/function.func-num-args.php
     *  @link   http://php.net/manual/en/language.functions.php
     */
    public function getOption($name, $fallback = null)
    {
        $name = $this->normalizeName($name, "retrieve");

        if (isset($this->options[$name])) {
            $option = $this->options[$name];
        }
        else if (2 <= func_num_args() ||
                array_key_exists($name, $this->options)) {
            $option = $fallback;
        }
        else {
            $message = "Could not retrieve unknown option: $name";
            throw new ConfigRuntimeError($message);
        }

        return $option;
    }

    /**
     *  Check whether a particular option exists
     *
     *  The hasOption() method will return TRUE in case a subsequent call
     *  to getOption() would succeed, when the same $name but no $fallback
     *  is provided. FALSE will be returned otherwise.
     *
     *  @param  string      $name       The name of the option to check
     *
     *  @return boolean
     *          TRUE is returned if the option exists, FALSE otherwise
     */
    public function hasOption($name)
    {
        try {
            $name = $this->normalizeName($name, "lookup");
            $bool = array_key_exists($name, $this->options);
        }
        catch (\Exception $error) {
            $bool = false;
            trigger_error("Caught $error", E_USER_WARNING);
        }

        return $bool;
    }

    /**
     *  A map of option names to option values
     *
     *  @var array
     */
    private $options = array();
}

