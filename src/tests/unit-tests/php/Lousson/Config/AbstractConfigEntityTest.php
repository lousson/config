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
 *  Lousson\Config\AbstractConfigEntityTest class definition
 *
 *  @package    org.lousson.config
 *  @copyright  (c) 2012 - 2013, The Lousson Project
 *  @license    http://opensource.org/licenses/bsd-license.php New BSD License
 *  @author     Attila Levai <alevai at quirkies.org>
 *  @author     Mathias J. Hennig <mhennig at quirkies.org>
 *  @filesource
 */
namespace Lousson\Config;

/** Dependencies: */
use Lousson\Config\AnyConfig;
use PHPUnit_Framework_TestCase;

/**
 *  Abstract test case for AnyConfig implementation
 *
 *  The Lousson\Config\AbstractConfigEntityTest class is an abstract unit-test
 *  for implementations of the AnyConfig interface. Authors may choose to
 *  use this class as the base for their own tests, which allows them to
 *  benefit from a basic coverage being provided already.
 *
 *  @since      lousson/Lousson_Config-0.1.0
 *  @package    org.lousson.config
 */
abstract class AbstractConfigEntityTest extends AbstractConfigTest
{
    /**
     *  Obtain the config entity to test
     *
     *  The getConfigEntity() method returns the AnyConfigEntity object
     *  that is about to be tested.
     *
     *  @return \Lousson\Config\AnyConfigEntity
     *          A config entity is returned on success
     */
    abstract public function getConfigEntity();

    /**
     *  Obtain the config to test
     *
     *  The getConfig() method returns the instance of the AnyConfig
     *  interface that is to be tested. It will be pre-set with the given
     *  $options.
     *
     *  @param  array   $options    The options to apply
     *
     *  @return \Lousson\Config\AnyConfig
     *          A config instance is returned on success
     */
    final public function getConfig(array $options)
    {
        $config = $this->getConfigEntity();

        foreach ($options as $key => $value) {
            $config->setOption($key, $value);
        }

        return $config;
    }
    
    /**
     *  Test the setOption() method
     *
     *  The testSetInvalidName() method is a test that verifies that any
     *  attempt to set an option with an invalid name will trigger an
     *  exception.
     *
     *  @param  string          $name               The config option name
     *  @param  array           $data               The config option data
     *
     *  @dataProvider           provideInvalidTestParameters
     *  @expectedException      Lousson\Config\AnyConfigException
     *  @test
     *
     *  @throws \Lousson\Config\AnyConfigException
     *          Raised in case the test is successful
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function testSetInvalidName($name, array $data)
    {
    	$config = $this->getConfig($data);
    	$config->setOption($name, $data);
    }
    
    /**
     *  Test the setOption() method
     *
     *  The testSetInvalidName() method is a test that verifies that any
     *  attempt to set an option with an invalid value will trigger an
     *  exception.
     *
     *  @param  string          $name               The config option name
     *  @param  array           $data               The config option data
     *
     *  @dataProvider           provideValidTestParameters
     *  @expectedException      Lousson\Config\AnyConfigException
     *  @test
     *
     *  @throws \Lousson\Config\AnyConfigException
     *          Raised in case the test is successful
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function testSetInvalidValue($name, array $data)
    {
    	$config = $this->getConfig($data);
    	$config->setOption($name, array("foo" => "bar", "baz"));
    }
    
    /**
     *  Test the delOption() method
     *
     *  The testDelInvalidOption() method is a test that verifies that the
     *  delOption() method raises an exception for invalid option names.
     *
     *  @param  string          $name               The config option name
     *  @param  array           $data               The config option data
     *
     *  @dataProvider           provideInvalidTestParameters
     *  @expectedException      Lousson\Config\AnyConfigException
     *  @test
     *
     *  @throws \Lousson\Config\AnyConfigException
     *          Raised in case the test is successful
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function getDelInvalidOption($name, array $data)
    {
    	$config = $this->getConfig($data);
    	$config->delOption($name);
    }
    
    /**
     *  Test the delOption() method
     *
     *  The testDelValidOption() method is a smoke test for the config's
     *  delOption() method.
     *
     *  @param  string          $name               The config option name
     *  @param  array           $data               The config option data
     *
     *  @dataProvider           provideValidTestParameters
     *  @test
     *
     *  @throws \PHPUnit_Framework_AssertionFailedError
     *          Raised in case an assertion has failed
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function testDelValidOption($name, array $data)
    {
    	$config = $this->getConfig($data);
    	$option = $config->delOption($name);
    	$expect = $config->hasOption($name);
    
    	$this->assertFalse(
    		$expect, sprintf(
    			"After invocation of %s::delOption(\"%s\") hasOption must".
    			" return false", get_class($config), $name
    		));
    }
}

