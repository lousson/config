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
 *  Lousson\Config\Builtin\BuiltinConfig class definition
 *
 *  @package    org.lousson.config
 *  @copyright  (c) 2012 - 2013, The Lousson Project
 *  @license    http://opensource.org/licenses/bsd-license.php New BSD License
 *  @author     Attila Levai <alevai at quirkies.org>
 *  @author     Mathias J. Hennig <mhennig at quirkies.org>
 *  @filesource
 */
namespace Lousson\Config\Builtin;

/** Dependencies: */
use Lousson\Config\AbstractConfig;

/** Exceptions: */
use Lousson\Config\Error\RuntimeConfigError;

/**
 *  Default implementation of the AnyConfig interface
 *
 *  The Lousson\Config\Builtin\BuiltinConfig class is the default
 *  implementation of the AnyConfig interface.
 *
 *  @since      lousson/Lousson_Config-0.1.0
 *  @package    org.lousson.config
 */
class BuiltinConfig extends AbstractConfig
{
    /**
     *  Create a config instance
     *
     *  The constructor allows to pass an array of $options that shall
     *  be available by default.
     *
     *  @param  array   $options    The default options, if any
     */
    public function __construct(array $options = array())
    {
        $this->options = $options;
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

        if (array_key_exists($name, $this->options)) {
            $option = $this->options[$name];
            $option = $this->normalizeValue($option, "retrieve");
        }
        else if(1 < func_num_args()) {
            $option = $fallback;
        }
        else {
            $message = "Could not retrieve unknown option: $name";
            throw new RuntimeConfigError($message);
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
            $bool && $this->normalizeValue($this->options[$name], "use");
        }
        catch (\Exception $error) {
            $bool = false;
            trigger_error("Caught $error", E_USER_WARNING);
        }

        return $bool;
    }

    /**
     *  The configuration options
     *
     *  @var array
     */
    private $options = array();
}

