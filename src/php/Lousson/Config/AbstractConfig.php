<?php
/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4 textwidth=75: *
 * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Copyright (c) 2013, The Lousson Project                               *
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
 *  Lousson\Config\AbstractConfig class declaration
 *
 *  @package    org.lousson.config
 *  @copyright  (c) 2013, The Lousson Project
 *  @license    http://opensource.org/licenses/bsd-license.php New BSD License
 *  @author     Mathias J. Hennig <mhennig at quirkies.org>
 *  @filesource
 */
namespace Lousson\Config;

/** Interfaces: */
use Lousson\Config\AnyConfig;

/** Dependencies: */
use Lousson\Record\Builtin\BuiltinRecordUtil;

/** Exceptions: */
use Lousson\Config\Error\ConfigRuntimeError;

/**
 *  An abstract base for AnyConfig implementations
 *
 *  The Lousson\Config\AbstractConfig class provides a set of utilities
 *  for authors implementing the AnyConfig interface.
 *
 *  @since      lousson/Lousson_Config-0.2.0
 *  @package    org.lousson.config
 */
abstract class AbstractConfig implements AnyConfig
{
    /**
     *  Normalize option names
     *
     *  The normalizeValue() method is used internally to normalize the
     *  given option $name. In case the $name is invalid, an expeption
     *  is thrown, warning that the $use of the option is not possible.
     *
     *  @param  mixed           $name               The option name
     *  @param  string          $use                The option use
     *
     *  @return mixed
     *          The normalized option name is returned on success
     *
     *  @throws \Lousson\Config\Error\ConfigRuntimeError
     *          Raised in case the option name is invalid
     */
    final protected function normalizeName($name, $use)
    {
        try {
            $normalized = BuiltinRecordUtil::normalizeName($name);
            return $normalized;
        }
        catch (\Lousson\Record\AnyRecordException $error) {
            $class = get_class($error);
            $message = "Could not $use option: Caught $class";
            $code = $error->getCode();
            throw new ConfigRuntimeError($message, $code, $error);
        }
    }

    /**
     *  Normalize option values
     *
     *  The normalizeValue() method is used internally to normalize the
     *  given option $value. In case the $value is invalid, an expeption
     *  is thrown, warning that the $use of the option is not possible.
     *
     *  @param  mixed           $value              The option value
     *  @param  string          $use                The option use
     *
     *  @return mixed
     *          The normalized option value is returned on success
     *
     *  @throws \Lousson\Config\Error\ConfigRuntimeError
     *          Raised in case the option value is invalid
     */
    final protected function normalizeValue($value, $use)
    {
        try {
            $normalized = BuiltinRecordUtil::normalizeItem($value);
            return $normalized;
        }
        catch (\Lousson\Record\AnyRecordException $error) {
            $class = get_class($error);
            $message = "Could not $use option: Caught $class";
            $code = $error->getCode();
            throw new ConfigRuntimeError($message, $code, $error);
        }
    }
}

