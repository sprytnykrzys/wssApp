<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\User;

trait AppController
{
    protected $DEBUG = true;
    /*
     * Token lifetime in hours
     */
    protected $tokenLifeTime = 1;
    protected $corsHeaders = array(
        "Access-Control-Allow-Origin" => '*',
        "Access-Control-Allow-Headers" => 'origin, content-type, accept',
        "Access-Control-Allow-Methods" => 'POST, GET, PUT, DELETE, PATCH, OPTIONS',
    );
    protected $request;
    protected $response     =   array();
    protected $salt         =   "9446979846cu4vj9d8fcgbzda9fy4nw46958bh4kiiut9iyp";
    protected $saltPasswd   =   "0023m4nfx90ayr7tyq2ERteEDFazsdfaubsdofasadfuycbu";

    /* user object necessary to check privileges */
    protected $user = null;

    /* create & update fields */
    protected $expectedParameters = array();
    protected $entityObject = null;
    protected $authenticated = false;

    /* file management essentials */
    protected $webDir = null;

    protected function authenticate($authenticate = true){
        if($this->DEBUG || $this->authenticated){
            return true;
        }
        $this->initializeControllerUtilityFields();
        if(!$authenticate){
            return true;
        }
        //$this->applyCORSHeaders();
        $this->request = Request::createFromGlobals();
        $ip = $this->request->getClientIp();

        $dataJSON = $this->getJSONRequest();
        $uid = isset($dataJSON['uid']) ? $dataJSON['uid'] : $this->request->get('uid');
        $token = isset($dataJSON['token']) ? $dataJSON['token'] : $this->request->get('token');
        
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle\Entity\User')->getByAuthData($uid, $token, $ip);

        if(count($user) == 0){
            $this->response['allow'] = 0;
            $this->response['errors'][] = 'Authentication required';
            return false;
        }
        $user = $user[0];
        if( !$this->isTokenValid($user) ){
            $this->response['allow'] = 0;
            $this->response['errors'][] = 'Authentication required. Token out of date.';
            return false;
        }
        $this->user = $user;
        $this->response['uid'] = $user->getId();
        $this->response['token'] = $this->generateTokenAndUpdate($user);
        /* multiple authentication protection */
        $this->authenticated = true;
        return true;
    }
    protected function isAdmin(){
        if($this->DEBUG){
            return true;
        }
        return !is_null($this->user) && is_object($this->user) && $this->user->getRole() == 'ADMIN';
    }
    public function tooFewPrivilegesResponse(){
        $view = $this->view(array_merge($this->getResponse(), array(
            'hasError' => 1,
            'errors' => ['too few privileges to do this operation']
        )), 403, $this->corsHeaders);
        return $this->handleView($view);
    }
    private function applyCORSHeaders(){
        foreach ($this->corsHeaders as $name => $val){
            header("$name: $val");
        };
//        $this->dump(headers_list(), false);
    }
    public function prepareAuthRequiredResponse(){
        $view = $this->view($this->getResponse(), 403, $this->corsHeaders);
        return $this->handleView($view);
    }
    public  function fastResponse($params, $code){
        $view = $this->view($params, $code, $this->corsHeaders);
        return $this->handleView($view);
    }
    /* Entity update related */
    public function prepareDuplicateEntity(){

    }
    private function isTokenValid($user){
        $time = getDate()[0];
        $tokenTime = $user->getTokenExpTime();
        $tokenTime = date_timestamp_get($tokenTime);
        return $time < $tokenTime;
    }
    protected function generateTokenAndUpdate($user, $loginProcess = false){
        $em = $this->getDoctrine()->getManager();

        $expTimestamp = getDate()[0]+(int)(3600 * $this->tokenLifeTime);
        $date = new \DateTime();
        $date->setTimestamp($expTimestamp);
        $token = hash( 'sha256', $expTimestamp.uniqid().$this->salt );
        $user->setToken( $token );
        $user->setTokenExpTime( $date );
        if($loginProcess){
            $user->setLastHost( $this->request->getClientIp() );
        }
        $em->persist( $user );
        $em->flush();

        return $token;
    }
    protected function hashPassword($pass){
        return hash( 'sha256', $pass.$this->saltPasswd );
    }
    protected function resp($array){
        if(is_array($array) && !array_key_exists('allow', $array) && !array_key_exists('uid', $array)){
            $this->response = array_merge($this->response, $array);
        }
    }
    protected function getResponse(){
        return $this->response;
    }
    protected function hasErrors(){
        $count = 0;
        if(isset($this->response['errors']) && $count = count($this->response['errors'])){
            return $count;
        }
        return $count;
    }
    protected function errorPush($val, $key = null){
        if( !isset($this->response['errors']) ){
            $this->response['errors'] = array();
        }
        if( !$key ){
            $this->response['errors'][] = $val;
        }
        else{
            $this->response['errors'][$key] = $val;
        }
    }
    protected function dump($arg, $json = true, $varDump = false){
        $this->applyCORSHeaders();
        if($json){
            echo json_encode($arg);
            return true;
        }
        echo "<pre>";
        if($varDump){
            var_dump($arg);
        }else{
            print_r($arg);
        }
        echo "</pre>";
    }
    protected function d($arg, $json = true){
        $this->applyCORSHeaders();
        $this->dump($arg, $json);
        exit;
    }
    protected function initializeControllerUtilityFields(){
        $this->webDir = $this->get('kernel')->getRootDir().'/../web/';
    }
    protected function getUrl( $path = null ){
        $request = Request::createFromGlobals();
        return $request->getScheme() . '://' . $request->getHttpHost() . $request->getBasePath() . $path;
    }
    /* pseudointerface */
    protected function handleExpectedParameters(){
        return array();
    }
    protected function traitDuplicateCriteria(){
        return array();
    }
    protected function objectUpdateOperations(){

        /* void - operate on $this->entityObject */
    }
    protected function returnResponseDuplicatedEntity(){
        $this->response['hasError'] = 1;
        $this->response['errors'] = array(
            'Duplicated entry'
        );
        $view = $this->view($this->getResponse(), 400, $this->corsHeaders);
        return $this->handleView($view);
    }
    protected function returnResponseEntryNotExist(){
        $this->response['hasError'] = 1;
        $this->response['errors'] = array(
            'Object with passed id not exist'
        );
        $view = $this->view($this->getResponse(), 400, $this->corsHeaders);
        return $this->handleView($view);
    }
    protected function checkEntityExist($repositoryName, $arguments, $em){
        $duplicates = $em->getRepository( $repositoryName )
            ->findBy($arguments);
        return count( $duplicates );
    }
    protected function traitImageAddToObject(){
        
    }

    protected function getJSONRequest(){
        $content = $this->get("request")->getContent();
        if (!empty($content))
        {
            $params = json_decode($content, true); // 2nd param to get as array
            if(is_array($params)){
                return $params;
            }
        }
        return false;
    }
}