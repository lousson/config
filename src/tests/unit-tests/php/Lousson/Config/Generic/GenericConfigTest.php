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
 *  Lousson\Config\GenericConfigTest class definition
 *
 *  @package    org.lousson.config
 *  @copyright  (c) 2012 - 2013, The Lousson Project
 *  @license    http://opensource.org/licenses/bsd-license.php New BSD License
 *  @author     Attila Levai <alevai at quirkies.org>
 *  @author     Mathias J. Hennig <mhennig at quirkies.org>
 *  @filesource
 */
namespace Lousson\Config\Generic;

/** Dependencies: */
use Lousson\Config\AbstractConfigTest;
use Lousson\Config\Generic\GenericConfig;

/**
 *  A test case for the GenericConfig implementation
 *
 *  @since      lousson/Lousson_Config-0.2.0
 *  @package    org.lousson.config
 */
final class GenericConfigTest extends AbstractConfigTest
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
    public function getConfig(array $options)
    {
        $config = new GenericConfig();

        foreach ($options as $name => $value) {
            $config->setOption($name, $value);
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
}

