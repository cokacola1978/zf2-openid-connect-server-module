<?php

namespace InoOicServer\Test\TestCase;

use Zend\Db;
use Zend\Config\Config;


abstract class AbstractDatabaseTestCase extends \PHPUnit_Extensions_Database_TestCase
{

    private static $pdo;

    private $conn;

    protected $dbConfig;


    final public function getConnection()
    {
        if ($this->conn === null) {
            if (self::$pdo == null) {
                self::$pdo = $this->getPdo();
            }
            $this->conn = $this->createDefaultDBConnection(self::$pdo, $this->getDbName());
        }
        
        return $this->conn;
    }


    /**
     * Workaround for https://github.com/sebastianbergmann/dbunit/issues/37.
     * 
     * @see https://github.com/sebastianbergmann/dbunit/issues/37#issuecomment-31069778
     * @return \PHPUnit_Extensions_Database_Operation
     */
    protected function getSetUpOperation()
    {
        return new \PHPUnit_Extensions_Database_Operation_Composite(array(
            \PHPUnit_Extensions_Database_Operation_Factory::DELETE_ALL(),
            \PHPUnit_Extensions_Database_Operation_Factory::INSERT()
        ));
    }


    protected function getDbAdapter()
    {
        return new Db\Adapter\Adapter($this->getDbConfig()
            ->get('adapter')
            ->toArray());
    }


    protected function getPdo()
    {
        return $this->getDbAdapter()
            ->getDriver()
            ->getConnection()
            ->getResource();
    }


    protected function getDbName()
    {
        return $this->getDbConfig()
            ->get('adapter')
            ->get('database');
    }


    protected function getDbConfig()
    {
        if (null === $this->dbConfig) {
            $this->dbConfig = new Config(require TESTS_CONFIG_DIR . 'db.cfg.php');
        }
        
        return $this->dbConfig;
    }
}