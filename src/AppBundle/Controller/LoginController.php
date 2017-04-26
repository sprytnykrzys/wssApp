<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Controller\AppController;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends FOSRestController
{
    use AppController;
    /**
     * @Route("/admin/login/")
     * @Method({"POST"})
     */
    public function adminLoginAction() {
        $this->request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();
        $mail = $pass =  null;
        if($dataJSON){
            $mail = isset($dataJSON['email']) ? $dataJSON['email'] : $this->request->get('email');
            $pass =  isset($dataJSON['password']) ? $dataJSON['password'] : $this->request->get('password');
        }
        else{
            $mail = $this->request->get('email');
            $pass = $this->request->get('password');
        }
        $pass = $this->hashPassword($pass);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle\Entity\User')->getByLoginCredentials($mail, $pass);
        if(!is_object($user)){
            $this->response['allow'] = 0;
            $this->response['errors'][] = 'Authentication failed. Wrong credentials provided.';
            $view = $this->view($this->getResponse(), 400, $this->corsHeaders);
            return $this->handleView($view);
        }
        $this->response['uid'] = $user->getId();
        $this->response['token'] = $this->generateTokenAndUpdate($user, true);
        $this->response['email'] = $user->getEmail();

        $view = $this->view($this->getResponse(), 200, $this->corsHeaders);
        return $this->handleView($view);
    }
}
