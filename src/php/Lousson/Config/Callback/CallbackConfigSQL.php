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
 *  Lousson\Config\Callback\CallbackConfigSQL class definition
 *
 *  @package    org.lousson.config
 *  @copyright  (c) 2012 - 2013, The Lousson Project
 *  @license    http://opensource.org/licenses/bsd-license.php New BSD
 *              License
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
use Lousson\Config\Error\ConfigArgumentError;

/**
 *  A Closure-based implementation of the AnyConfigEntity interface
 *
 *  The Lousson\Config\Callback\CallbackConfigSQL is a flexible
 *  implementation of the AnyConfigEntity interface, using a Closure to
 *  retrieve config values.
 *  
 *  This implementation communicates with the closure via SQL, enabling
 *  storage of the configuration data in a SQL compatible storage engine.
 *
 *  @since      lousson/Lousson_Config-0.2.0
 *  @package    org.lousson.config
 */
class CallbackConfigSQL
    extends AbstractConfig
    implements AnyConfigEntity
{

    /**
     * Prefix string indicating that the following data is json encoded
     *
     * Constant holing the internaly used prefix for storing multi-
     * dimensional data in a SQL data field. The prefix is appended
     * to field-content which contains json-encoded data. This way
     * it can be identified whether the data is json encoded or not.
     *
     * @var string
     */
    const JSON_INDICATOR = 'json:';

    /**
     *  Create a config instance
     *
     *  The constructor allows to pass a Closure $getter that is used to
     *  retrieve configuration values. This callback must provide the exact
     *  same interface as the getOption() method, otherwise the behavior
     *  is undefined.
     *
     *  @param Closure          $callback   The config callback
     *  @param string           $tableName  The name of the SQL table to
     *                                      use in all outgoing statements
     *  @param string           $keyField   The name of the column
     *                                      representing the property name
     *  @param string           $valueField The name of the column
     *                                      containing the values
     *  @param array (optional) $index      array containing additional
     *                                      key/value restrictions to apply
     *                                      to the WHERE-part of all
     *                                      outgoing SQL queries Expects
     *                                      column names as keys and their
     *                                      required values as array values
     */
    public function __construct(
        Closure $callback,
        $tableName,
        $keyField,
        $valueField,
        array $index = array()
    ) {

        $this->generateSql($valueField, $tableName, $index, $keyField);

        $this->callback = $callback;

    }

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

        if (is_array($value) || null === $value) {
            $value = self::JSON_INDICATOR . json_encode($value);
        }

        $exists = $this->hasOption($name);

        $stack = $exists ? $this->sqlUpdate : $this->sqlInsert;
        $format = array_shift($stack);
        array_unshift($stack, $value);
        array_unshift($stack, $format);
        $stack[] = $name;

        try {
            call_user_func_array($this->callback, $stack);
        }
        catch (\Exception $error) {
            $class = get_class($error);
            $message = "Could not write option: Caught $class";
            $code = $error->getCode();
            throw new ConfigRuntimeError($message, $code, $error);
        }
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

        $stack = $this->sqlDelete;
        $stack[] = $name;

        try {
            call_user_func_array($this->callback, $stack);
        }
        catch (\Exception $error) {
            $class = get_class($error);
            $message = "Could not write option: Caught $class";
            $code = $error->getCode();
            throw new ConfigRuntimeError($message, $code, $error);
        }
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
        $stack = $this->sqlSelect;
        $stack[] = $name;
        $useFallback = (null !== $fallback || 2 <= func_num_args());

        try {
            $value = call_user_func_array($this->callback, $stack);
        }
        catch (\Exception $error) {
            $class = get_class($error);
            $message = "Could not retrieve option: Caught $class";
            $code = $error->getCode();
            throw new ConfigRuntimeError($message, $code, $error);
        }
        $value = $this->normalizeValue($value, "retrieve");
        if (null === $value) {
            if ($useFallback) {
                $value = $fallback;
            }
            else {
                $message = "Could not retrieve unknown option: $name";
                throw new ConfigRuntimeError($message);
            }
        }
        elseif (0 === strpos($value, self::JSON_INDICATOR)) {
            $value = json_decode(
                substr(
                    $value,
                    strlen(self::JSON_INDICATOR)
                ),
                true
            );
        }

        return $value;
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
            $name = $this->normalizeName($name, "retrieve");
            $stack = $this->sqlSelect;
            $stack[] = $name;
            $value = call_user_func_array($this->callback, $stack);
               $result = false;
            if (null !== $value) {
                $value = $this->normalizeValue($value, "retrieve");
                $result = true;
            }
        }
        catch (\Exception $error) {
            $result = false;
            trigger_error("Caught $error", E_USER_WARNING);
        }

        return $result;
    }

    /**
     * Internal function for transcribng the input data into SQL queries
     * 
     * The generateSql method will fill private properties of this class
     * with SQL statements for SELECT, INSERT, UPDATE and delete. This is
     * done in preparation for later read/write operations invoked by the
     * set/get/hasOption methods.
     * 
     * @param string $valueField    The name of the column containing
     *                              the values
     * @param string $tableName        The name of the SQL table
     *                              to use in all outgoing statements
     * @param array $index            array containing additional key/value
     *                              restrictions to apply to the WHERE-
     *                              part of all outgoing SQL queries
     *                              Expects column names as keys and
     *                              their required values as array values
     * @param string $keyField        The name of the column representing
     *                              the property name
     */
    private function generateSql(
        $valueField,
        $tableName,
        $index,
        $keyField
    ){

        $sqlSelect = array(
            "SELECT {$valueField} FROM {$tableName} WHERE"
        );
        $sqlInsert = array(
            "INSERT INTO {$tableName} SET {$valueField} = '%s',"
        );
        $sqlUpdate = array(
            "UPDATE {$tableName} SET {$valueField} = '%s' WHERE"
        );
        $sqlDelete = array(
            "DELETE FROM {$tableName} WHERE"
        );

        $stack = array();

        //fill where with key-value pairs from $index array
        foreach ($index as $key => $value) {
            $delimiter = " AND";
            $delimiterInsert = ", ";
            $fragment = "{$key} = '%s'";
            $sqlSelect[] = $fragment . $delimiter;
            $sqlInsert[] = $fragment . $delimiterInsert;
            $sqlUpdate[] = $fragment . $delimiter;
            $sqlDelete[] = $fragment . $delimiter;
            $stack[] = $value;
        }

        //fill where with key-value pairs from $index array
        $fragment = "{$keyField} = '%s'\n";
        $sqlSelect[] = $fragment;
        $sqlInsert[] = $fragment;
        $sqlUpdate[] = $fragment;
        $sqlDelete[] = $fragment;

        $this->sqlSelect = array_merge(
            array(implode(" ", $sqlSelect)),
            $stack
        );
        $this->sqlInsert = array_merge(
            array(implode(" ", $sqlInsert)),
            $stack
        );
        $this->sqlUpdate = array_merge(
            array(implode(" ", $sqlUpdate)),
            $stack
        );
        $this->sqlDelete = array_merge(
            array(implode(" ", $sqlDelete)),
            $stack
        );

    }

    /**
     * printf format statement (with args) for SELECT queries
     * 
     * Internal property holding the SQL query for SELECT operations
     * used in getOption and hasOption methods. This property is filled
     * by internal method "generateSql". Contains a printf-format string
     * in the first array element and their associated fill-in args as
     * following elements.
     * 
     * @var array
     */
    private $sqlSelect;

    /**
     * printf format statement (with args) for INSERT queries
     * 
     * Internal property holding the SQL query for INSERT operations
     * used in getOption and hasOption methods. This property is filled
     * by internal method "generateSql". Contains a printf-format string
     * in the first array element and their associated fill-in args as
     * following elements.
     * 
     * @var array
     */
    private $sqlInsert;

    /**
     * printf format statement (with args) for UPDATE queries
     * 
     * Internal property holding the SQL query for UPDATE operations
     * used in getOption and hasOption methods. This property is filled
     * by internal method "generateSql". Contains a printf-format string
     * in the first array element and their associated fill-in args as
     * following elements.
     * 
     * @var array
     */
    private $sqlUpdate;

    /**
     * printf format statement (with args) for DELETE queries
     * 
     * Internal property holding the SQL query for DELETE operations
     * used in getOption and hasOption methods. This property is filled
     * by internal method "generateSql". Contains a printf-format string
     * in the first array element and their associated fill-in args as
     * following elements.
     * 
     * @var array
     */
    private $sqlDelete;

    /**
     * The configuration callback for data interaction.
     * 
     * Should expect SQL format string from one of the according
     * properties ($sqlSelect, $sqlInsert, $sqlUpdate, $sqlDelete)
     * as first parameter, and args for filling into the SQL format string
     * by sprintf. Should return the read data in case of SELECT queries
     * and null in case of data manipulation queries.
     *
     * @var \Closure
     */
    private $callback;

}
