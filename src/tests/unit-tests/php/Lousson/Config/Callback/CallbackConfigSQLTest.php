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
 *  Lousson\Config\Callback\CallbackConfigSQLTest class definition
 *
 *  @package    org.lousson.config
 *  @copyright  (c) 2012 - 2013, The Lousson Project
 *  @license    http://opensource.org/licenses/bsd-license.php New BSD License
 *  @author     Nils Gotzhein <nils.gotzhein at gmail.com>
 *  @filesource
 */
namespace Lousson\Config\Callback;

/** Dependencies: */
use Lousson\Config\AbstractConfigEntityTest;
use Lousson\Config\Callback\CallbackConfigSQL;
use Closure;

/**
 *  A test case for the CallbackConfigSQL implementation
 *
 *  @since      lousson/Lousson_Config-0.2.0
 *  @package    org.lousson.config
 */
class CallbackConfigSQLTest extends AbstractConfigEntityTest
{
    /**
     * Example value for the parameter $tableName of the constructor
     * of the tested class.
     *
     * @var string
     */
    const TABLE_NAME = 'table';
    
    /**
     * Example value for the parameter $keyField of the constructor
     * of the tested class.
     *
     * @var string
     */
    const KEY_FIELD = 'key_field';
    
    /**
     * Example value for the parameter $valueField of the constructor
     * of the tested class.
     *
     * @var string
     */
    const VALUE_FIELD = 'valueField';
    
    /**
     *  Obtain the config entity to test
     *
     *  The getConfigEntity() method returns the AnyConfigEntity object
     *  that is about to be tested.
     *
     *    @param    $callback    Closure
     *  @return \Lousson\Config\AnyConfigEntity
     *          A config entity is returned on success
     */
    public function getConfigEntity( Closure $callback = null )
    {
         if(null === $callback) {
            $callback = $this->getCallback();
         }
        
        $index = array(
            'entity_key' => 'entity_value'
        );
        
        $config = new CallbackConfigSQL(
            $callback,
            self::TABLE_NAME,
            self::KEY_FIELD,
            self::VALUE_FIELD,
            $index
        );
        return $config;
    }

    /**
     *  Test the internal error handling
     *
     *  The testGetErrorOption() method is a test case that verifies that
     *  non-config exceptions raised by the callback do not lead to a
     *  violation of the AnyConfig interface.
     *
     *  @expectedException          Lousson\Config\Error\ConfigRuntimeError
     *  @test
     *
     *  @throws \Lousson\Config\Error\ConfigRuntimeError
     *          Raised in case the test is successful
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function testGetErrorOption()
    {
        $callback = function($name, $fallback) {
            throw new \DomainException("foo bar baz");
        };

        $config = $this->getConfigEntity($callback);
        $config->getOption("test");
    }
    
    /**
     *  Test the internal error handling
     *
     *  The testSetErrorOption() method is a test case that verifies that
     *  non-config exceptions raised by the callback do not lead to a
     *  violation of the AnyConfig interface.
     *
     *  @expectedException          Lousson\Config\Error\ConfigRuntimeError
     *  @test
     *
     *  @throws \Lousson\Config\Error\ConfigRuntimeError
     *          Raised in case the test is successful
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function testSetErrorOption()
    {
        $callback = function() {
            $parameter = func_get_args();
            $format = array_shift($parameter);
            $operation = strstr($format, ' ', true);
            if('INSERT' === $operation || 'UPDATE' === $operation) {
                throw new \DomainException("foo bar baz");
            }
        };
    
        $config = $this->getConfigEntity($callback);
        $config->setOption("test", null);
    }
    
    /**
     *  Test the internal error handling
     *
     *  The testDelErrorOption() method is a test case that verifies that
     *  non-config exceptions raised by the callback do not lead to a
     *  violation of the AnyConfig interface.
     *
     *  @expectedException          Lousson\Config\Error\ConfigRuntimeError
     *  @test
     *
     *  @throws \Lousson\Config\Error\ConfigRuntimeError
     *          Raised in case the test is successful
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function testDelErrorOption()
    {
        $callback = function($name, $fallback) {
            throw new \DomainException("foo bar baz");
        };
    
        $config = $this->getConfigEntity($callback);
        $config->delOption("test");
    }

    /**
     *  Test the internal error handling
     *
     *  The testHasErrorOption() method is a test case that verifies that
     *  non-config exceptions raised by the callback do not lead to a
     *  violation of the AnyConfig interface.
     *
     *  @expectedException          PHPUnit_Framework_Error_Warning
     *  @test
     *
     *  @throws \PHPUnit_Framework_Error_Warning
     *          Raised in case the test is successful
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function testHasErrorOption()
    {
        $callback = function($name, &$fallback) {
            throw new \DomainException("foo bar baz");
        };

        $config = new CallbackConfigSQL($callback);
        $config->hasOption("test");
    }

    /**
     * Method providing a valid callback for passing to the constructor
     * of the tested class. Analyzes the inpute data and stores it in a
     * local static cache for testing.
     * 
     * @return mixed
     */
    final protected function getCallback() {
        $test = $this;
        return function() use($test) {
            static $cache = array();
            $parameter = func_get_args();
            $format = array_shift($parameter);
            $test->assertInternalType("string", $format);
            $operation = strstr($format, ' ', true);
            if('INSERT' === $operation || 'UPDATE' === $operation) {
                $value = array_shift($parameter);
            }

            $cacheKey = implode( ':', $parameter );

            if('INSERT' === $operation || 'UPDATE' === $operation) {
                $cache[$cacheKey] = $value;
            }
            elseif('DELETE' === $operation && array_key_exists( $cacheKey, $cache )) {
                unset( $cache[ $cacheKey ] );
            }
            elseif('SELECT' === $operation) {
                $res = array_key_exists( $cacheKey, $cache ) ? $cache[ $cacheKey ] : null;
                return $res;
            }
            
        };

    }

}

