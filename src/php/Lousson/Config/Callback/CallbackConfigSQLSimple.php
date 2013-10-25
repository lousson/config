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
 *  Lousson\Config\Callback\CallbackConfigSQLSimple class definition
 *
 *  @package    org.lousson.config
 *  @copyright  (c) 2012 - 2013, The Lousson Project
 *  @license    http://opensource.org/licenses/bsd-license.php New BSD
                License
 *  @author     Nils Gotzhein <nils.gotzhein at gmail.com>
 *  @filesource
 */
namespace Lousson\Config\Callback;

/** Dependencies: */
use Lousson\Config\AbstractConfig;
use Lousson\Config\AnyConfigEntity;
use Closure;

/** Exceptions: */
use Lousson\Config\Error\ConfigRuntimeError;

/**
 * A Closure-based implementation of the AnyConfigEntity interface
 *
 * The Lousson\Config\Callback\CallbackConfigSQL is a flexible
 * implementation of the AnyConfigEntity interface, using a Closure to
 * retrieve config values.
 *  
 * This implementation calls the closure, passing prepared SQL compatible
 * statements. The Closure is meant to use the prepared SQL statements to
 * communicate with an SQL compatible storage engine.
 *  
 * The Closure expects sprintf-compatible parameters. For example, the
 * closure could generate the sql query with the following command:
 * $sql = call_user_func_array( 'sprintf', func_get_args() );
 * @see CallbackConfigSQLTest::getCallback() for an example.  
 *  
 * Compared to its parent class, this variant offers a few less parameters
 * to pass, which is therefore easier to handle and to implement, but also
 * less flexible this way.
 *
 *  @since      lousson/Lousson_Config-0.2.0
 *  @package    org.lousson.config
 */
class CallbackConfigSQLSimple
    extends CallbackConfigSQL
    implements AnyConfigEntity
{
    /**
     *  Create a config instance
     *
     *  The constructor allows to pass a Closure $callback that is used to
     *  retrieve configuration values. This callback must provide the exact
     *  same interface as the getOption() method, otherwise the behavior
     *  is undefined.
     *
     *  @param  Closure          $callback      The config callback
     *  @param  string           $tablePrefix   The prefix for the name of
     *                                          the SQL table. (The full
     *                                          table name is stored in a
     *                                          constant)
     *  @param  array (optional) $index         array containing additional
     *                                          key/value restrictions to
     *                                          apply to the WHERE-part of
     *                                          all outgoing SQL queries
     *                                          Expects column names as
     *                                          keys and their required
     *                                          values as array values
     */
    public function __construct(
        Closure $callback,
        $tablePrefix,
        array $index = array()
    ) {

        $tableName  = $tablePrefix . self::TABLE_NAME;
        $keyField   = self::KEY_FIELD;
        $valueField = self::VALUE_FIELD;
        parent::__construct(
            $callback,
            $tableName,
            $keyField,
            $valueField,
            $index
        );

    }

    /**
     * Default value for the parameter $tableName of the constructor
     * of the parent class.
     *  
     * @var string
     */
    const TABLE_NAME = 'config';

    /**
     * Default value for the parameter $keyField of the constructor
     * of the parent class.
     * 
     * @var string
     */
    const KEY_FIELD = 'option_name';

    /**
     * Default value for the parameter $valueField of the constructor
     * of the parent class.
     * 
     * @var string
     */
    const VALUE_FIELD = 'option_value';

}
