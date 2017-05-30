<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Test;
use AppBundle\Controller\AppController;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


class TestController extends FOSRestController
{
    use AppController;

    /**
     * @Route("/test")
     * @Method({"POST"})
     */
    public function addObjectAction(){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }

        $request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle\Entity\Test');

        $id = isset($dataJSON['id']) ? $dataJSON['id'] : $request->get('id');
        $text = isset($dataJSON['text']) ? $dataJSON['text'] : $request->get('text');
        $text2 = isset($dataJSON['text2']) ? $dataJSON['text2'] : $request->get('text2');

        if(!is_null($id)){
            $new = $repo->find($id);
            if(!is_object($new)){
                $new = new Test();
            }
        }
        else{
            $new = new Test();
        }
        $new->setText($text);
        $new->setText2($text2);

        $em->persist($new);
        $em->flush();

        return $this->fastResponse(array(
            'ok' => 1,
            'object' => $this->prepareObject($new)
        ));

    }

    /**
     * @Route("/test")
     * @Method({"GET"})
     */
    public function getObjectsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle\Entity\Test');
        $objects = $repo->findAll();
        $resp = array();
        foreach ($objects as $index => $object) {
            $resp[] = $this->prepareObject($object);
        }
        return $this->fastResponse(array(
            'ok' => 1,
            'objects' => $resp
        ));
    }

    public function prepareObject($obj){
        return array(
            'id' => $obj->getId(),
            'text' => $obj->getText(),
            'text2' => $obj->getText2()
        );
    }

}