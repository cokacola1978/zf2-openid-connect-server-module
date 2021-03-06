<?php

namespace InoOicServer\General\Factory;

use InoOicServer\ServiceManager;


class AbstractAdapterFactory implements \Zend\ServiceManager\FactoryInterface
{

    const CONFIG_FIELD = 'generic';

    const NS = __NAMESPACE__;


    public function createService(\Zend\ServiceManager\ServiceLocatorInterface $serviceLocator)
    {
        $configField = static::CONFIG_FIELD;
        
        $config = $serviceLocator->get('Config');
        
        if (! isset($config['oic_server'][$configField])) {
            throw new ServiceManager\Exception\ConfigNotFoundException($configField);
        }
        
        $storageConfig = $config['oic_server'][$configField];
        
        if (! isset($storageConfig['type'])) {
            throw new ServiceManager\Exception\ConfigNotFoundException($configField . '/type');
        }
        
        $className = sprintf("%s\\%s", $this->_getNs(), $storageConfig['type']);
        
        $options = array();
        if (isset($storageConfig['options']) && is_array($storageConfig['options'])) {
            $options = $storageConfig['options'];
        }
        
        return new $className($options);
    }


    protected function _getNs()
    {
        return static::NS;
    }
}