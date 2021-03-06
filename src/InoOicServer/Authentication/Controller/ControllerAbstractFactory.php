<?php

namespace InoOicServer\Authentication\Controller;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceLocatorInterface;
use Zend\ServiceManager\AbstractFactoryInterface;
use InoOicServer\ServiceManager\Exception as ServiceManagerException;


/**
 * Factory for authentication controllers.
 *
 */
class ControllerAbstractFactory implements AbstractFactoryInterface
{


    /**
     * {@inheritdoc}
     * @see \Zend\ServiceManager\AbstractFactoryInterface::canCreateServiceWithName()
     */
    public function canCreateServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        return (null !== $this->getControllerConfig($serviceLocator, $requestedName));
    }


    /**
     * {@inheritdoc}
     * @see \Zend\ServiceManager\AbstractFactoryInterface::createServiceWithName()
     */
    public function createServiceWithName(ServiceLocatorInterface $serviceLocator, $name, $requestedName)
    {
        $controllerConfig = $this->getControllerConfig($serviceLocator, $requestedName);
        if (! $controllerConfig) {
            throw new ServiceManagerException\ConfigNotFoundException("authentication_handlers/$requestedName");
        }
        
        if (! isset($controllerConfig['class'])) {
            throw new ServiceManagerException\ConfigNotFoundException("authentication_handlers/$requestedName/class");
        }
        
        $className = $controllerConfig['class'];
        if (! class_exists($className)) {
            throw new ServiceManagerException\ClassNotFoundException($className);
        }
        
        $controller = new $className();
        /* @var $controller \InoOicServer\Authentication\Controller\AbstractController */
        if (! ($controller instanceof AuthenticationControllerInterface)) {
            throw new Exception\InvalidControllerException(sprintf("Controller '%s' is not a valid authentication controller", $requestedName));
        }
        
        $options = array(
            'label' => $requestedName
        );
        
        if (isset($controllerConfig['options']) && is_array($controllerConfig['options'])) {
            $options = $controllerConfig['options'] + $options;
        }
        
        $controller->setOptions($options);
        
        $sm = $this->getServiceManager($serviceLocator);
        
        $controller->setUserFactory($sm->get('InoOicServer\UserFactory'));
        $controller->setUserInputFilterFactory($sm->get('InoOicServer\InputFilterFactory'));
        $controller->setAuthorizeContextManager($sm->get('InoOicServer\AuthorizeContextManager'));
        $controller->setLogger($sm->get('InoOicServer\Logger'));
        
        return $controller;
    }


    /**
     * Returns the configuration for the controller associated with the provided name.
     * 
     * @param ServiceLocatorInterface $serviceLocator
     * @param string $requestedName
     * @throws ServiceManagerException\ConfigNotFoundException
     * @return array|null
     */
    protected function getControllerConfig(ServiceLocatorInterface $serviceLocator, $requestedName)
    {
        $config = $this->getServiceManager($serviceLocator)->get('Config');

        if (! isset($config['oic_server']['authentication_handlers']) || ! is_array($config['oic_server']['authentication_handlers'])) {
            throw new ServiceManagerException\ConfigNotFoundException('authentication_handlers');
        }

        $authConfig = $config['oic_server']['authentication_handlers'];
        
        if (! isset($authConfig[$requestedName]) || ! is_array($authConfig[$requestedName])) {
            return null;
        }
 
        return $authConfig[$requestedName];
    }


    /**
     * Returns the global service manager object.
     * 
     * @param ServiceLocatorInterface $serviceLocator
     * @return ServiceManager
     */
    protected function getServiceManager(ServiceLocatorInterface $serviceLocator)
    {
        return $serviceLocator->getServiceLocator();
    }
}