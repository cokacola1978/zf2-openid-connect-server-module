<?php

namespace PhpIdServer;

return array(
    
    'logger' => array(
        'writers' => array(
            array(
                'name' => 'stream', 
                'options' => array(
                    'stream' => '/data/var/log/devel/phpid-server/phpid-server.log'
                )
            )
        )
    ), 
    
    'client_registry_storage' => array(
        'storage' => '\PhpIdServer\Client\Registry\Storage\SingleJsonFileStorage', 
        'options' => array(
            'json_file' => 'data/client/metadata.json'
        )
    ), 
    
    'authentication' => array(
        'handler_endpoint_route' => 'php-id-server/authentication-endpoint-dummy'
    ), 
    
    'session_storage' => array(
        'type' => 'MysqlLite', 
        'options' => array(
            
            'session_table' => 'session',
            'authorization_code_table' => 'authorization_code',
            'access_token_table' => 'access_token',
            'refresh_token_table' => 'refresh_token',
            
            'adapter' => array(
                'driver' => 'Pdo_Mysql', 
                'host' => 'localhost', 
                'username' => 'phpidserver', 
                'password' => 'heslp pro id server', 
                'database' => 'phpid'
            )
        )
    ), 
    
    'user_serializer' => array(
        'options' => array(
            'adapter' => array(
                'name' => 'PhpSerialize', 
                'options' => array()
            )
        )
    )
);

