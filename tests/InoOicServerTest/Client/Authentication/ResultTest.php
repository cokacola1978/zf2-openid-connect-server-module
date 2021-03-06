<?php

namespace InoOicServerTest\Client\Authentication;

use InoOicServer\Client\Authentication\Result;


class ResultTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @var Result
     */
    protected $result = null;

    protected $authMethodClass = 'AuthMethod';


    public function setUp()
    {
        $this->result = new Result($this->authMethodClass);
    }


    public function testConstructorWithImplicitArgs()
    {
        $result = new Result($this->authMethodClass);
        $this->assertSame($this->authMethodClass, $result->getMethodClass());
        $this->assertFalse($result->isAuthenticated());
    }


    public function testSetResult()
    {
        $authenticated = false;
        $reason = 'error';
        
        $this->result->setResult($authenticated, $reason);
        $this->assertSame($authenticated, $this->result->isAuthenticated());
        $this->assertSame($reason, $this->result->getNotAuthenticatedReason());
    }


    public function testSetAuthenticated()
    {
        $this->result->setAuthenticated();
        $this->assertTrue($this->result->isAuthenticated());
    }


    public function testSetNotAuthenticated()
    {
        $reason = 'error auth';
        $this->result->setNotAuthenticated($reason);
        $this->assertFalse($this->result->isAuthenticated());
        $this->assertSame($reason, $this->result->getNotAuthenticatedReason());
    }
}