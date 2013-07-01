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
 *  Lousson\Config\AbstractConfigTest class definition
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
 *  The Lousson\Config\AbstractConfigTest class is an abstract unit-test
 *  for implementations of the AnyConfig interface. Authors may choose to
 *  use this class as the base for their own tests, which allows them to
 *  benefit from a basic coverage being provided already.
 *
 *  @since      lousson/Lousson_Config-0.1.0
 *  @package    org.lousson.config
 */
abstract class AbstractConfigTest extends PHPUnit_Framework_TestCase
{
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
    abstract function getConfig(array $options);

    /**
     *  Obtain valid test data
     *
     *  The getValidTestData() method returns an array of multiple items,
     *  each of whose is an array representing a valid config record.
     *
     *  @return array
     *          A list of valid config records is returned on success
     */
    public function getValidTestData()
    {
        $data[] = array("foo" => "bar", "baz" => null);
        $data[] = array("foo" => array("bar" => "baz"));

        return $data;
    }

    /**
     *  Obtain invalid test data
     *
     *  The getInvalidTestData() method returns an array of multiple items,
     *  each of whose is an array representing an invalid config record.
     *
     *  @return array
     *          A list of invalid config records is returned on success
     */
    public function getInvalidTestData()
    {
        $data[] = array(1, 2, 3, 4, 5);
        $data[] = array(" :invalid" => "data");

        return $data;
    }

    /**
     *  Provide valid test parameters
     *
     *  The provideValidTestParameters() method returns an array of
     *  multiple items, each of whose is an array of two items:
     *
     *- A valid option name and
     *- An associative array of option names and values
     *
     *  @return array
     *          A list of valid test parameters is returned on success
     */
    public function provideValidTestParameters()
    {
        $parameters = array();

        foreach ($this->getValidTestData() as $data) {
            foreach (array_keys($data) as $name) {
                $parameters[] = array($name, $data);
            }
        }

        return $parameters;
    }

    /**
     *  Provide invalid test parameters
     *
     *  The provideInvalidTestParameters() method returns an array of
     *  multiple items, each of whose is an array of two items:
     *
     *- An invalid option name and
     *- An associative array of option names and values
     *
     *  @return array
     *          A list of invalid test parameters is returned on success
     */
    public function provideInvalidTestParameters()
    {
        $parameters[] = array(" :invalid", array("foo" => "bar"));
        $parameters[] = array("123456789", array("foo" => "bar"));

        return $parameters;
    }

    /**
     *  Test the getOption() method
     *
     *  The testGetValidOption() method is a smoke test for the config's
     *  getOption() method.
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
    public function testGetValidOption($name, array $data)
    {
        $config = $this->getConfig($data);
        $expect = $data[$name];
        $option = $config->getOption($name);

        $this->assertEquals(
            $expect, $option, sprintf(
            "The invocation of %s::getOption(\"%s\") must return the ".
            "expected value", get_class($config), $name
        ));
    }

    /**
     *  Test the getOption() method
     *
     *  The testGetFallbackOption() method is a test that verifies that the
     *  getOption() method returns the fallback for absent options, if any.
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
    public function testGetFallbackOption($name, array $data)
    {
        $config = $this->getConfig($data);
        $name = md5("salt:$name");
        $option = $config->getOption($name, $this);

        $this->assertSame(
            $this, $option, sprintf(
            "The invocation of %s::getOption(\"%s\") must return the ".
            "expected value", get_class($config), $name
        ));
    }

    /**
     *  Test the getOption() method
     *
     *  The testGetInvalidOption() method is a test that verifies that the
     *  getOption() method raises an exception for absent option names.
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
    public function getGetAbsentOption($name, array $data)
    {
        $config = $this->getConfig($data);
        $name = md5("salt:$name");
        $config->getOption($name);
    }

    /**
     *  Test the getOption() method
     *
     *  The testGetInvalidOption() method is a test that verifies that the
     *  getOption() method raises an exception for invalid option names.
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
    public function getGetInvalidOption($name, array $data)
    {
        $config = $this->getConfig($data);
        $config->getOption($name);
    }

    /**
     *  Test the hasOption() method
     *
     *  The testHasValidOption() method is a smoke test for the config's
     *  hasOption() method.
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
    public function testHasValidOption($name, array $data)
    {
        $config = $this->getConfig($data);
        $hasOption = $config->hasOption($name);

        $this->assertTrue(
            $hasOption, sprintf(
            "The invocation of %s::hasOption(\"%s\") must return the ".
            "expected value", get_class($config), $name
        ));
    }

    /**
     *  Test the hasOption() method
     *
     *  The testHasAbsentOption() method is a test to verify that the
     *  hasOption() method returns FALSE for absent options.
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
    public function testHasAbsentOption($name, array $data)
    {
        $config = $this->getConfig($data);
        $name = md5("salt:$name");
        $hasOption = $config->hasOption($name);

        $this->assertFalse(
            $hasOption, sprintf(
            "The invocation of %s::hasOption(\"%s\") must return the ".
            "expected value", get_class($config), $name
        ));
    }

    /**
     *  Test the hasOption() method
     *
     *  The testHasInvalidOption() method is a test to verify that the
     *  hasOption() method issues a warning for invalid option names.
     *
     *  @param  string          $name               The config option name
     *  @param  array           $data               The config option data
     *
     *  @dataProvider           provideInvalidTestParameters
     *  @expectedException      PHPUnit_Framework_Error_Warning
     *  @test
     *
     *  @throws \PHPUnit_Framework_Error_Warning
     *          Raised in case the test is successful
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function testHasInvalidOption($name, array $data)
    {
        $config = $this->getConfig($data);
        $config->hasOption($name);
    }
}

