<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Client;
use AppBundle\Controller\AppController;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends FOSRestController
{
    use AppController;
    const KEY = 'rmuwt6546wel4t65';

    /* compatibility workaround */
    /**
     * @Route("/client/delete")
     * @Method({"POST"})
     */
    public function postDeleteGetAction(){
        return $this->deleteGetAction();
    }

    /* compatibility workaround */
    /**
     * @Route("/client/{id}/delete")
     * @Method({"POST"})
     */
    public function postDeleteAction($id = null){
        return $this->deleteAction($id);
    }

    /**
     * @Route("/client")
     * @Method({"POST"})
     */
    public function clientAddAction(){
        $request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        $id = isset($dataJSON['client']['id']) ? $dataJSON['client']['id'] : $request->get('id');

        return $this->updateAction($id);
    }
    /**
     * @Route("/client/{id}/")
     * @Method({"POST"})
     */
    public function updateAction($id = null){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }
        $request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        $em = $this->getDoctrine()->getManager();

        if(is_null($id)){
            $id = isset($dataJSON['client']['id']) ? $dataJSON['client']['id'] : $request->get('id');
        }
        $name = isset($dataJSON['client']['name']) ? $dataJSON['client']['name'] : null;
        $discount = isset($dataJSON['client']['discount']) ? $dataJSON['client']['discount'] : null;

        if(!is_null($id)){
            $client = $em->getRepository('AppBundle\Entity\Client')->find($id);
            if(!is_object($client)){
                $client = new Client();
                $now = new \DateTime("now");
                $client->setCreationDate($now);
            }
        }
        else{
            $client = new Client();
            $now = new \DateTime("now");
            $client->setCreationDate($now);
        }
        if(!is_null($name)){
            $client->setName($name);
        }
        if(!is_null($discount)){
            $client->setDiscount($discount);
        }
        $em->persist( $client );
        $em->flush();

        return $this->fastResponse([
            'success' => 1,
            'client' => $this->prepareClientObject($client),
            'message' => array(
                'client added successfully'
            )
        ] , 200);
    }

    /**
     * @Route("/client")
     * @Method({"GET"})
     */
    public function getClientAction(){
        $request = Request::createFromGlobals();
        $dataJSON = $this->getJSONRequest();

        $id = isset($dataJSON['id']) ? $dataJSON['id'] : $request->get('id');
        if(!is_null($id)){
            return $this->getClientsByIdAction($id);
        }
        else{
            $em = $this->getDoctrine()->getManager();
            $users = $em->getRepository('AppBundle\Entity\Client')->findAll();

            return $this->fastResponse(array(
                'clients' => $this->prepareClientObjects($users)
            ), 200);
        }
    }

    /**
     * @Route("/client/{id}/")
     * @Method({"GET"})
     */
    public function getClientsByIdAction($id = null){
        if(!is_null($id)){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle\Entity\Client')->find($id);

            return $this->fastResponse(array(
                'client' => $this->prepareClientObjects($user)
            ), 200);
        }
        else{
            $this->response['hasError'] = 1;
            $this->response['message'] = 'Client with ID = '. $id .' doesn\'t exist';
            return $this->fastResponse($this->response, 400);
        }

    }

    /**
     * @Route("/client")
     * @Method({"DELETE"})
     */
    public function deleteGetAction(){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        $request = Request::createFromGlobals();
        $dataJSON = $this->getJSONRequest();

        $id = isset($dataJSON['id']) ? $dataJSON['id'] : $request->get('id');
        return $this->deleteAction($id);
    }


    /**
     * @Route("/client/{id}/")
     * @Method({"DELETE"})
     */
    public function deleteAction($id = null){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }
        if(!is_null($id)){
            $em = $this->getDoctrine()->getManager();
            $client = $em->getRepository('AppBundle\Entity\Client')->find($id);
            if(is_object($client)){
                $em->remove( $client );
                $em->flush();

                $this->response['success'] = 1;
                $this->response['message'] = 'Client with ID = '. $id .' has been removed';
                return $this->fastResponse($this->response, 200);
            }
            else{
                $this->response['hasError'] = 1;
                $this->response['message'] = 'Client with ID = '. $id .' doesn\'t exist';
                return $this->fastResponse($this->response, 400);
            }
        }
        $this->response['hasError'] = 1;
        $this->response['message'] = 'ID is null';
        return $this->fastResponse($this->response, 400);
    }
    private function prepareClientObjects($arr){
        if(is_object($arr)){
            return $this->prepareClientObject($arr);
        }
        elseif (is_array($arr)){
            $clients = array();
            foreach( $arr as $client ){
                $clients[] = $this->prepareClientObject($client);
            }
            return $clients;
        }
        else{
            return array();
        }
    }

    private function prepareClientObject($obj){
        if(is_object($obj)){
            return array(
                'id' => $obj->getId(),
                'name' => $obj->getName(),
                'discount' => $obj->getDiscount()
            );
        }
        else{
            return array();
        }
    }
}