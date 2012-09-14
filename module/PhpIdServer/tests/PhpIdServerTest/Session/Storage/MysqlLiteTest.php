<?php

namespace PhpIdServerTest\Session\Storage;

use PhpIdServer\Session\Token\AuthorizationCode;
use PhpIdServerTest\Framework\Config;
use PhpIdServer\Session\Session;
use PhpIdServer\Session\Storage;


class MysqlLiteTest extends \PHPUnit_Extensions_Database_TestCase
{

    /**
     * Raw DB PDO connection.
     * 
     * @var \PHPUnit_Extensions_Database_DB_DefaultDatabaseConnection
     */
    protected $_conn = NULL;

    /**
     * Session storage object.
     * @var Storage\MysqlLite
     */
    protected $_storage = NULL;


    /**
     * (non-PHPdoc)
     * @see PHPUnit_Extensions_Database_TestCase::getConnection()
     * @return \PHPUnit_Extensions_Database_DB_IDatabaseConnection
     */
    public function getConnection ()
    {
        if (NULL === $this->_conn) {
            $pdoConfig = Config::get()->mysql_lite->adapter->toArray();
            $pdoConfig['raw_driver'] = 'mysql';
            
            $dsn = sprintf("%s:dbname=%s;host=%s", $pdoConfig['raw_driver'], $pdoConfig['database'], $pdoConfig['host']);
            $pdo = new \PDO($dsn, $pdoConfig['username'], $pdoConfig['password']);
            
            $this->_conn = $this->createDefaultDBConnection($pdo);
        }
        
        return $this->_conn;
    }


    /**
     * (non-PHPdoc)
     * @see PHPUnit_Extensions_Database_TestCase::getDataSet()
     * @return \PHPUnit_Extensions_Database_DataSet_IDataSet
     */
    public function getDataSet ()
    {
        return $this->createFlatXMLDataSet(Config::get()->db->dataset);
    }


    public function setUp ()
    {
        parent::setUp();
        
        $config = Config::get()->mysql_lite;
        $this->_storage = new Storage\MysqlLite($config);
    }


    public function testSaveLoadSession ()
    {
        $session = $this->_createSession();
        
        $this->_storage->saveSession($session);
        
        $loadedSession = $this->_storage->loadSession($session->getId());
        
        $this->assertInstanceOf('\PhpIdServer\Session\Session', $loadedSession);
        $this->assertEquals($loadedSession->toArray(), $session->toArray());
    }


    public function testSaveLoadAuthorizationCode ()
    {
        $authorizationCode = $this->_createAuthorizationCode();
        
        $this->_storage->saveAuthorizationCode($authorizationCode);
        $loadedAuthorizationCode = $this->_storage->loadAuthorizationCode($authorizationCode->getCode());
        
        $this->assertInstanceOf('\PhpIdServer\Session\Token\AuthorizationCode', $loadedAuthorizationCode);
        $this->assertEquals($loadedAuthorizationCode->toArray(), $authorizationCode->toArray());
    }


    protected function _createSession ()
    {
        $data = array(
            Session::FIELD_ID => 'session_id_123', 
            Session::FIELD_USER_ID => 'testuser', 
            Session::FIELD_AUTHENTICATION_TIME => '2012-08-01 00:00:00', 
            Session::FIELD_AUTHENTICATION_METHOD => 'dummy', 
            Session::FIELD_CREATE_TIME => new \DateTime('now'), 
            Session::FIELD_MODIFY_TIME => '2012-09-09 00:00:00', 
            Session::FIELD_EXPIRATION_TIME => '2012-09-13 00:00:00', 
            Session::FIELD_USER_DATA => 'serialized_user_data_123'
        );
        
        return new Session($data);
    }


    protected function _createAuthorizationCode ()
    {
        $data = array(
            AuthorizationCode::FIELD_CODE => 'authorization_code_123', 
            AuthorizationCode::FIELD_SESSION_ID => 'session_id_456', 
            AuthorizationCode::FIELD_ISSUE_TIME => new \DateTime('now'), 
            AuthorizationCode::FIELD_EXPIRATION_TIME => new \DateTime('tomorrow'), 
            AuthorizationCode::FIELD_CLIENT_ID => 'testclient', 
            AuthorizationCode::FIELD_SCOPE => 'openid'
        );
        
        return new AuthorizationCode($data);
    }
}