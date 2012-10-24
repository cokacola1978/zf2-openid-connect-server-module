<?php

namespace PhpIdServer\ServiceManager;

use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\Config;
use PhpIdServer\User;
use PhpIdServer\Authentication;


class ServiceManagerConfig extends Config
{


    public function getFactories ()
    {
        return array(
            
            /*
             * Main logger object
             */
            'Logger' => function  (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                $loggerConfig = $config['logger'];
                if (! isset($loggerConfig['writers'])) {
                    throw new Exception\ConfigNotFoundException('logger/writers');
                }
                
                $logger = new \Zend\Log\Logger();
                if (count($loggerConfig['writers'])) {
                    $priority = 1;
                    foreach ($loggerConfig['writers'] as $writerConfig) {
                        $logger->addWriter($writerConfig['name'], $priority ++, $writerConfig['options']);
                    }
                }
                
                return $logger;
            }, 
            
            /*
             * User/Serializer
             */
            'UserSerializer' => function  (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['user_serializer'])) {
                    throw new Exception\ConfigNotFoundException('user_serializer');
                }
                
                return new User\Serializer\Serializer($config['user_serializer']);
            },
            
            /*
             * Session/IdGenerator
             */
            'SessionIdGenerator' => function  (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['session_id_generator'])) {
                    throw new Exception\ConfigNotFoundException('session_id_generator');
                }
                
                $generatorConfig = $config['session_id_generator'];
                
                if (! isset($generatorConfig['class'])) {
                    throw new Exception\ConfigNotFoundException('session_id_generator/class');
                }
                
                $className = $generatorConfig['class'];
                
                $options = array();
                if (isset($generatorConfig['options']) && is_array($generatorConfig['options'])) {
                    $options = $generatorConfig['options'];
                }
                
                return new $className($options);
            },
            
            /*
             * Authentication/Manager
             */
            'AuthenticationManager' => function  (ServiceManager $sm)
            {
                $config = $sm->get('Config');
                if (! isset($config['authentication'])) {
                    throw new Exception\ConfigNotFoundException('authentication');
                }
                
                return new Authentication\Manager($config['authentication']);
            }
        );
    }
}