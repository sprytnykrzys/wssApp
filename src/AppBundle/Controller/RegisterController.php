<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class RegisterController extends FOSRestController
{
    use AppController;

    const TOKEN = 'rmuwt6546wel4t65';
    /* BACKEND: Incompatible Controller class implemented. This controller will be implemented in future release. */

    /**
     * @Route("/user")
     * @Method({"POST"})
     */
    public function registerAction(){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        $uid = isset($dataJSON['uid']) ? $dataJSON['uid'] : $request->get('uid');
        if(!is_null($uid)){
            $user = $em->getRepository('AppBundle\Entity\User')->find($uid);
            if(is_object($user)){
                return $this->updateAction($uid);
            }
        }

        $email = isset($dataJSON['email']) ? $dataJSON['email'] : $request->get('email');
        $password =  isset($dataJSON['password']) ? $dataJSON['password'] : $request->get('password');
        $role =  isset($dataJSON['role']) ? $dataJSON['role'] : $request->get('role');
        $id_client = isset($dataJSON['id_client']) ? $dataJSON['id_client'] : $request->get('id_client');

        if(isset($role)){
            $role = mb_convert_case($role,  MB_CASE_UPPER);
        }
        else{
            $role = 'CLIENT';
        }

        if($password != null && $email != null){
            $user = new User();

            $password = $this->hashPassword($password);

            $user->setPassword( $password);
            $user->setEmail($email);

            $user->setRole($role);
            $user->setDiscount('0 zł');

            $now = new \DateTime("now");
            $exp = $now->add( new \DateInterval("PT1H") );
            $user->setCreationDate( $now );
            $user->setTokenExpTime( $exp );
            $user->setToken( "00000000000000000000000000000000000000000" );
            $user->setLastHost( "127.0.0.1" );
            $user->setLastLogin( $now );

            if(!is_null($id_client)){
                /* TODO: dodac walidacje czy klient istnieje */
                $user->setIdClient($id_client);
            }

            $em->persist($user);
            if( !$em->flush() ){
                return $this->fastResponse([
                    'user' => array(
                        'uid' => $user->getId(),
                        'token' => $user->getToken(),
                        'email' => $user->getEmail(),
                        'role' => $user->getRole(),
                    )
                ] , 200);
            }
            return $this->fastResponse([
                'hasErrors' => 1,
                'errors' => array(
                    'error in adding process'
                )
            ] , 400);
        }
        return $this->fastResponse([
            'hasErrors' => 1,
            'errors' => array(
                'This service is not available yet'
            )
        ] , 400);
    }
    /**
     * @Route("/user/{uid}/")
     * @Method({"POST"})
     */
    public function updateAction($uid = null){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        $request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        $key = isset($dataJSON['key']) ? $dataJSON['key'] : $request->get('key');

        if( !$key ||
            (null != $key && $key != self::TOKEN) ||
            $key != self::TOKEN
        ){
            return $this->fastResponse([
                'hasErrors' => 1,
                'errors' => array(
                    'This service is not available'
                )
            ] , 400);
        }

        $email = isset($dataJSON['email']) ? $dataJSON['email'] : $request->get('email');
        $password = isset($dataJSON['password']) ? $dataJSON['password'] : $request->get('password');
        $role = isset($dataJSON['role']) ? $dataJSON['role'] : $request->get('role');
        $discount = isset($dataJSON['discount']) ? $dataJSON['discount'] : $request->get('discount');
        $id_client = isset($dataJSON['id_client']) ? $dataJSON['id_client'] : $request->get('id_client');

        if(!is_null($uid)){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle\Entity\User')->find($uid);
            if(is_object($user)){
                if(!is_null($email)){
                    $user->setEmail($email);
                }
                if(!is_null($password)){
                    $password = $this->hashPassword($password);
                    $user->setPassword($password);
                }
                if(!is_null($role)){
                    $user->setRole($role);
                }
                if(!is_null($discount)){
                    $user->setDiscount($discount);
                }
                if(!is_null($id_client)){
                    /* TODO: dodac walidacje czy klient istnieje */
                    $user->setIdClient($id_client);
                }


                $em->persist($user);
                $em->flush();
                return $this->fastResponse(array(
                    'user' => $this->prepareUserObjects($user)
                ), 200);
            }
            else{
                return $this->registerAction();
            }
        }
        $this->response['hasError'] = 1;
        $this->response['message'] = 'User with ID = '. $uid .' doesn\'t exist';
        return $this->fastResponse($this->response, 400);

    }

    /**
     * @Route("/user")
     * @Method({"GET"})
     */
    public function getUserAction(){
        $request = Request::createFromGlobals();
        $dataJSON = $this->getJSONRequest();

        $uid = isset($dataJSON['uid']) ? $dataJSON['uid'] : $request->get('uid');
        $recently_logged = isset($dataJSON['recently_logged']) ? $dataJSON['recently_logged'] : $request->get('recently_logged');
        if(!is_null($uid)){
            return $this->getUserByUidAction($uid);
        }
        else{
            $em = $this->getDoctrine()->getManager();
            if(!is_null($recently_logged)){
                $users = $em->getRepository('AppBundle\Entity\User')->findRecentlyLoggedUsers();
            }
            else{
                $users = $em->getRepository('AppBundle\Entity\User')->findAllClients();
            }

            return $this->fastResponse(array(
                'users' => $this->prepareUserObjects($users)
            ), 200);
        }
    }

    /**
     * @Route("/user/{uid}/")
     * @Method({"GET"})
     */
    public function getUserByUidAction($uid = null){
        if(!is_null($uid)){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle\Entity\User')->find($uid);

            return $this->fastResponse(array(
                'user' => $this->prepareUserObjects($user)
            ), 200);
        }
        else{
            $this->response['hasError'] = 1;
            $this->response['message'] = 'User with ID = '. $uid .' doesn\'t exist';
            return $this->fastResponse($this->response, 400);
        }

    }
    /**
     * @Route("/user")
     * @Method({"DELETE"})
     */
    public function deleteGetAction(){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        $request = Request::createFromGlobals();
        $dataJSON = $this->getJSONRequest();

        $uid = isset($dataJSON['uid']) ? $dataJSON['uid'] : $request->get('uid');
        return $this->deleteAction($uid);
    }
    /**
     * @Route("/user/{uid}/delete/")
     * @Method({"POST"})
     */

    public function postDeleteAction($uid = null){
        return $this->deleteAction($uid);
    }
    /**
     * @Route("/user/{uid}/")
     * @Method({"DELETE"})
     */
    public function deleteAction($uid = null){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }

        if(!is_null($uid)){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle\Entity\User')->find($uid);
            if(is_object($user)){
                $em->remove( $user );
                $em->flush();

                $this->response['success'] = 1;
                $this->response['message'] = 'User with ID = '. $uid .' has been removed';
                return $this->fastResponse($this->response, 200);
            }
            else{
                $this->response['hasError'] = 1;
                $this->response['message'] = 'User with ID = '. $uid .' doesn\'t exist';
                return $this->fastResponse($this->response, 400);
            }
        }
        $this->response['hasError'] = 1;
        $this->response['message'] = 'ID is null';
        return $this->fastResponse($this->response, 400);
    }
    private function prepareUserObjects($arr){
        if(is_object($arr)){
            return $this->prepareUserObject($arr);
        }
        elseif (is_array($arr)){
            $users = array();
            foreach( $arr as $user ){
                $users[] = $this->prepareUserObject($user);
            }
            return $users;
        }
        else{
            return array();
        }
    }
    private function prepareUserObject($obj){
        if(is_object($obj)){
            return array(
                'uid' => $obj->getId(),
                'email' => $obj->getEmail(),
                'id_client' => $obj->getIdClient(),
                'role' => $obj->getRole(),
                'discount' => $obj->getDiscount(),
                'last_login' => $obj->getLastLogin(),
                'creation_date' => $obj->getCreationDate(),
            );
        }
        else{
            return array();
        }
    }
}
