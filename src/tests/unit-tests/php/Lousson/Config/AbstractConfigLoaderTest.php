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
 *  Lousson\Config\AbstractConfigLoaderTest class definition
 *
 *  @package    org.lousson.config
 *  @copyright  (c) 2013, The Lousson Project
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
 *  Abstract test case for AnyConfigLoader implementations
 *
 *  The Lousson\Config\AbstractConfigLoaderTest class is a base class for
 *  testing implementations of the AnyConfigLoader interface.
 *
 *  @since      lousson/Lousson_Config-0.2.0
 *  @package    org.lousson.config
 */
abstract class AbstractConfigLoaderTest extends PHPUnit_Framework_TestCase
{
    /**
     *  Obtain a loader instance
     *
     *  The getConfigLoader() method returns the config loader instance
     *  used in the test cases.
     *
     *  @return \Lousson\Config\AnyConfigLoader
     *          A config loader instance is returned on success
     */
    abstract public function getConfigLoader();

    /**
     *  Provide smokeTest() parameters
     *
     *  The provideSmokeTestParameters() method will return a list of
     *  multiple items, each of whose is an array of either two or three
     *  parameters:
     *
     *- A byte sequence of configuration data
     *- An array of configuration options represented by the sequence
     *- The (internet-) media-type of the byte sequence
     *
     *  @return array
     *          A list of smoke test parameters is returned on success
     */
    public function provideSmokeTestParameters()
    {
        $d[] = array("foo" => "bar");
        $d[] = array("foo" => "bar", "baz" => array(1, 2, 3, 4));
        $d[] = array();

        foreach ($d as $data) {
            $json = json_encode($data);
            $p[] = array($json, $data, "application/json");
            $p[] = array($json, $data, "text/json");
        }

        return $p;
    }

    /**
     *  Test the loadConfig() method
     *
     *  The smokeTest() method is a test case for the loader's loadConfig()
     *  method. It will prepare a temporary file with the given $content,
     *  before invoking the loader and comparing the values returned by the
     *  so-created configuration object with the $expected ones.
     *
     *  @param  string              $content        The configuration bytes
     *  @param  array               $expected       The expected values
     *  @param  string              $type           The media type, if any
     *
     *  @dataProvider               provideSmokeTestParameters
     *  @test
     *
     *  @throws \PHPUnit_Framework_AssertionFailedError
     *          Raised in case an assertion has failed
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function smokeTest($content, array $expected, $type = null)
    {
        $tempdir = sys_get_temp_dir();
        $tempnam = tempnam($tempdir, "lousson-test-");
        $success = file_put_contents($tempnam, $content);

        $this->assertNotEquals(
            false, $success, sprintf(
            "The invocation of the file_put_contents() method for \"%s\" ".
            "is expected to be successful", $tempnam
        ));

        $this->tempFiles[] = $tempnam;

        $loader = $this->getConfigLoader();
        $config = $loader->loadConfig($tempnam, $type);

        $this->assertInstanceOf(
            "Lousson\\Config\\AnyConfig", $config, sprintf(
            "The %s::loadConfig() method must return an instance of the ".
            "AnyConfig interface", get_class($loader)
        ));

        foreach ($expected as $key => $value) {
            $option = $config->getOption($key);
            $this->assertEquals($value, $option);
        }
    }

    /**
     *  Test the loadConfig() method
     *
     *  The errorTest() method is a test case for the loader's loadConfig()
     *  method. It attempts to provoke an exception by invoking the method
     *  with an invalid location parameter.
     *
     *  @expectedException          Lousson\Config\AnyConfigException
     *  @test
     *
     *  @throws \Lousson\Config\AnyConfigException
     *          Raised in case the test is successful
     *
     *  @throws \Exception
     *          Raised in case of an implementation error
     */
    public function errorTest()
    {
        $loader = $this->getConfigLoader();
        $loader->loadConfig("invalid://foo/bar/baz");
    }

    /**
     *  Cleanup
     *
     *  The destructor has been implemented explicitely to unlink all
     *  temporary files created during the tests.
     */
    public function __destruct()
    {
        array_map("unlink", $this->tempFiles);
    }

    /**
     *  A list of temporary files created
     *
     *  @var array
     */
    private $tempFiles = array();
}

