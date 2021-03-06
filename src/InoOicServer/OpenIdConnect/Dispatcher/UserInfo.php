<?php

namespace InoOicServer\OpenIdConnect\Dispatcher;

use InoOicServer\General\Exception as GeneralException;
use InoOicServer\OpenIdConnect\Request;
use InoOicServer\OpenIdConnect\Response;
use InoOicServer\User\UserInterface;
use InoOicServer\User;


/**
 * Dispatches a "userinfo" request.
 *
 */
class UserInfo extends AbstractDispatcher
{

    /**
     * The user info request.
     * 
     * @var Request\UserInfo
     */
    protected $request;

    /**
     * The user info response.
     * 
     * @var Response\UserInfo
     */
    protected $response;

    /**
     * The userinfo mapper.
     * 
     * @var User\UserInfo\Mapper\MapperInterface
     */
    protected $userInfoMapper;


    /**
     * Sets the user info request.
     * 
     * @param Request\UserInfo $request
     */
    public function setUserInfoRequest (Request\UserInfo $request)
    {
        $this->request = $request;
    }


    /**
     * Returns the user info request.
     * 
     * @return Request\UserInfo
     */
    public function getUserInfoRequest ()
    {
        return $this->request;
    }


    /**
     * Sets the user info response.
     * 
     * @param Response\UserInfo $response
     */
    public function setUserInfoResponse (Response\UserInfo $response)
    {
        $this->response = $response;
    }


    /**
     * Returns the user info response.
     * 
     * @return Response\UserInfo
     */
    public function getUserInfoResponse ()
    {
        return $this->response;
    }


    /**
     * Sets the userinfo mapper.
     * 
     * @param User\UserInfo\Mapper\MapperInterface $mapper
     */
    public function setUserInfoMapper (User\UserInfo\Mapper\MapperInterface $mapper)
    {
        $this->userInfoMapper = $mapper;
    }


    /**
     * Returns the userinfo mapper.
     * 
     * @return User\UserInfo\Mapper\MapperInterface
     */
    public function getUserInfoMapper ()
    {
        if (null === $this->userInfoMapper) {
            $this->userInfoMapper = new User\UserInfo\Mapper\ToArray();
        }
        
        return $this->userInfoMapper;
    }


    /**
     * Dispatches the user info request.
     * 
     * @throws GeneralException\MissingDependencyException
     * @return Response\UserInfo
     */
    public function dispatch ()
    {
        $request = $this->getUserInfoRequest();
        if (! $request) {
            throw new GeneralException\MissingDependencyException('userinfo request');
        }
        
        /*
         * Validate request.
         */
        if (! $request->isValid()) {
            return $this->errorResponse(Response\UserInfo::ERROR_INVALID_REQUEST, sprintf("Reasons: %s", implode(', ', $request->getInvalidReasons())));
        }
        
        /*
         * Validate token and retrieve session.
         */
        $sessionManager = $this->getSessionManager(true);
        
        $accessToken = $sessionManager->getAccessToken($request->getAuthorizationValue());
        if (! $accessToken) {
            return $this->errorResponse(Response\UserInfo::ERROR_INVALID_TOKEN_NOT_FOUND, 'No such access token');
        }
        
        if ($accessToken->isExpired()) {
            return $this->errorResponse(Response\UserInfo::ERROR_INVALID_TOKEN_EXPIRED, 'Expired access token');
        }
        
        $session = $sessionManager->getSessionByAccessToken($accessToken);
        if (! $session) {
            return $this->errorResponse(Response\UserInfo::ERROR_INVALID_TOKEN_NO_SESSION, 'No session associated with the access token');
        }
        
        /*
         * Retrieve user info and return response.
        */
        $user = $sessionManager->getUserFromSession($session);
        if (! $user) {
            return $this->errorResponse(Response\UserInfo::ERROR_INVALID_TOKEN_NO_USER_DATA, 'Could not extract user data');
        }
        
        // FIXME - validate user data
        

        return $this->validResponse($user);
    }


    /**
     * Returns the user info response with the user data.
     * 
     * @param UserInterface $user
     * @throws GeneralException\MissingDependencyException
     * @return Response\UserInfo
     */
    protected function validResponse (UserInterface $user)
    {
        $userInfoResponse = $this->getUserInfoResponse();
        if (! $userInfoResponse) {
            throw new GeneralException\MissingDependencyException('userinfo response');
        }
        
        $userInfoResponse->setUserData($this->getUserInfoMapper()
            ->getUserInfoData($user));
        
        return $userInfoResponse;
    }


    /**
     * Returns an error response with the provided message.
     *
     * @param string $message
     * @throws GeneralException\MissingDependencyException
     * @return Response\Token
     */
    protected function errorResponse ($message, $description = NULL)
    {
        $userInfoResponse = $this->getUserInfoResponse();
        if (! $userInfoResponse) {
            throw new GeneralException\MissingDependencyException('userinfo response');
        }
        
        $userInfoResponse->setError($message, $description);
        
        return $userInfoResponse;
    }
}