<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AppController as AppController;
use FOS\RestBundle\Controller\FOSRestController as FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends FOSRestController
{
    use AppController;
    /**
     * @Route("/")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
//        if( !$this->authenticate()){
//            return $this->prepareAuthRequiredResponse();
//        }
        // replace this example code with whatever you need
        $this->resp(array(
            'page' => 'index'
        ));
        $view = $this->view($this->getResponse(), 200, $this->corsHeaders);
        return $this->handleView($view);
    }
    private function metodsFixForPHPSTORM(){
        new Route(array());
        new Method(array());
    }
}
