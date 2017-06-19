<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;


class LoginController extends FOSRestController
{
    use AppController;
    /**
     * @Route("/user/login")
     * @Method({"POST"})
     */
    public function loginAction() {
        $this->request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        $mail = isset($dataJSON['auth']['email']) ? $dataJSON['auth']['email'] : $this->request->get('email');
        $pass =  isset($dataJSON['auth']['password']) ? $dataJSON['auth']['password'] : $this->request->get('password');

        $pass = $this->hashPassword($pass);

        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository('AppBundle\Entity\User')->getByLoginCredentials($mail, $pass);
        if(!is_object($user)){
            $this->response['allow'] = 0;
            $this->response['errors'][] = 'Authentication failed. Wrong credentials provided.';
            $view = $this->view($this->getResponse(), 400, $this->corsHeaders);
            return $this->handleView($view);
        }
        $now = new \DateTime("now");
        $user->setLastLogin($now);
        $em->persist( $user );
        $em->flush();

        $this->response['auth'] = array(
            'uid' => $user->getId(),
            'token' => $this->generateTokenAndUpdate($user, true),
            'role' => $user->getRole(),
            'email' => $user->getEmail()
        );
        $view = $this->view($this->getResponse(), 200, $this->corsHeaders);
        return $this->handleView($view);
    }
}
