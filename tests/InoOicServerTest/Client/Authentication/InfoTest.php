<?php

namespace InoOicServerTest\Client\Authentication;

use InoOicServer\Client\Authentication\Info;
use InoOicServer\Client\Authentication;


class InfoTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Info
     */
    protected $info = null;


    public function setUp()
    {
        $clientId = 'abc';
        $method = 'secret';
        $options = array(
            'foo' => 'bar'
        );
        
        $this->info = new Info($clientId, $method, $options);
    }


    public function testConstructor()
    {
        $this->assertSame('abc', $this->info->getClientId());
        $this->assertSame('secret', $this->info->getMethod());
        $this->assertSame(array(
            'foo' => 'bar'
        ), $this->info->getOptions());
    }


    public function testGetOption()
    {
        $this->assertSame('bar', $this->info->getOption('foo'));
    }


    public function testGetOptionNonExistent()
    {
        $this->assertNull($this->info->getOption('nonexistent'));
    }
}