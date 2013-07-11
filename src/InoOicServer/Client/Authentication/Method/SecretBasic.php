<?php

namespace InoOicServer\Client\Authentication\Method;

use InoOicServer\Client;
use Zend\Http;


class SecretBasic extends AbstractMethod
{

    const AUTH_OPTION_SECRET = 'secret';


    /**
     * {@inhertidoc}
     * @see \InoOicServer\Client\Authentication\Method\MethodInterface::authenticate()
     */
    public function authenticate(Client\Authentication\Info $info, Http\Request $httpRequest)
    {
        /* @var $httpRequest \Zend\Http\Request */
        $authorizationHeader = $httpRequest->getHeader('Authorization');
        if (! $authorizationHeader) {
            return $this->createFailureResult('Missing authorization header');
        }
        
        $value = $authorizationHeader->getFieldValue();
        $parts = explode(' ', $value);
        if ('basic' !== strtolower($parts[0])) {
            return $this->createFailureResult(sprintf("Unsupported authorization '%s'", $parts[0]));
        }
        
        if (! isset($parts[1])) {
            return $this->createFailureResult('Missing authorization hash');
        }
        
        $receivedHash = $parts[1];
        $clientHash = base64_encode(sprintf("%s:%s", $info->getClientId(), $info->getOption(self::AUTH_OPTION_SECRET)));
        
        if ($receivedHash !== $clientHash) {
            return $this->createFailureResult('Invalid authorization');
        }
        
        return $this->createSuccessResult();
    }
}