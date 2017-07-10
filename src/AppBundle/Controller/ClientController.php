<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AppController as AppController;
use AppBundle\Entity\Client;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ClientController extends FOSRestController
{
    use AppController;
    const KEY = 'rmuwt6546wel4t65';

    /**
     * @Route("/client/generate_offer/")
     * @Method({"POST"})
     */
    public function generateOfferAction(){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isClient()){
            return $this->tooFewPrivilegesResponse();
        }
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle\Entity\Client');
        if(is_object($this->user) && $id_client = $this->user->getIdClient()){
            $client = $repo->find($id_client);
            if(is_object($client)){
                $client->incrementGeneratedOffers();
                $em->flush();
                return $this->fastResponse([
                    'success' => 1,
                    'client' => $this->prepareClientObject($client),
                    'message' => array(
                        'offer counter increased'
                    )
                ] , 200);
            }
        }
        return $this->fastResponse([
            'hasError' => 1,
            'errors' => array(
                'cannot find client for user'
            )
        ] , 400);
    }

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
        $errors = [];
        $request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        $em = $this->getDoctrine()->getManager();
        $new = false;

        if(is_null($id)){
            $id = isset($dataJSON['client']['id']) ? $dataJSON['client']['id'] : $request->get('id');
        }
        $name = isset($dataJSON['client']['name']) ? $dataJSON['client']['name'] : null;
        $discount = isset($dataJSON['client']['discount']) ? $dataJSON['client']['discount'] : null;
        $discountCurrency = isset($dataJSON['client']['discount_currency']) ? $dataJSON['client']['discount_currency'] : null;

        if(!is_null($id)){
            $client = $em->getRepository('AppBundle\Entity\Client')->find($id);
            if(!is_object($client)){
                $client = new Client();
                $now = new \DateTime("now");
                $client->setCreationDate($now);
                $client->setLoginCount(0);
                $client->setGeneratedOffersCount(0);
                $new = true;
            }
        }
        else{
            $client = new Client();
            $now = new \DateTime("now");
            $client->setCreationDate($now);
            $client->setLoginCount(0);
            $client->setGeneratedOffersCount(0);
            $new = true;
        }
        if(!is_null($name)){
            $client->setName($name);
        }
        if(!is_null($discount)){
            if(!is_numeric($discount)){
                $errors[] = 'discount must be numeric';
            }
            else{
                $client->setDiscount($discount);
            }
        }
        if(!is_null($discountCurrency)){
            $client->setDiscountCurrency($discountCurrency);
        }

        if(!empty($errors)){
            return $this->fastResponse([
                'errors' => $errors
            ] , 200);
        }

        $em->persist( $client );
        $em->flush();

        return $this->fastResponse([
            'success' => 1,
            'client' => $this->prepareClientObject($client),
            'message' => array(
                ($new ? 'client added successfully' : 'client updated successfully')
            )
        ] , 200);
    }

    /**
     * @Route("/client")
     * @Method({"GET"})
     */
    public function getClientAction(){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }

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
                'clients' => $this->prepareClientObjects($users, true),
                'general_stats' => $this->getGeneralStats(),
            ), 200);
        }
    }

    /**
     * @Route("/client/{id}/")
     * @Method({"GET"})
     */
    public function getClientsByIdAction($id = null){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }

        if(!is_null($id)){
            $em = $this->getDoctrine()->getManager();
            $user = $em->getRepository('AppBundle\Entity\Client')->find($id);

            return $this->fastResponse(array(
                'client' => $this->prepareClientObjects($user, true),
                'general_stats' => $this->getGeneralStats(),
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
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
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
                $users = $em->getRepository('AppBundle\Entity\User')->findBy(array(
                    'role' => 'CLIENT',
                    'idClient' => $client->getId(),
                ));
                foreach($users as $user){
                    $em->remove( $user );
                }
                $em->remove( $client );
                $em->flush();

                $this->response['success'] = 1;
                $this->response['message'] = 'Client with ID = '. $id .' has been removed with their associated users';
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

    private function prepareClientObjects($arr, $withUsers = false){
        if(is_object($arr)){
            return $this->prepareClientObject($arr, $withUsers);
        }
        elseif (is_array($arr)){
            $clients = array();
            foreach( $arr as $client ){
                $clients[] = $this->prepareClientObject($client, $withUsers);
            }
            return $clients;
        }
        else{
            return array();
        }
    }

    /* update information in client stats */

    private function prepareClientObject($obj, $withUsers = false){
        if(is_object($obj)){
            $ret = array(
                'id' => $obj->getId(),
                'name' => $obj->getName(),
                'discount' => $obj->getDiscount(),
                'creation_date' => $obj->getCreationDate(),
                'stats' => array(
                    'login_count' => $obj->getLoginCount(),
                    'offers_count' => $obj->getGeneratedOffersCount(),
                ),
            );
            if($withUsers){
                $em = $this->getDoctrine()->getManager();
                $users = $em->getRepository('AppBundle\Entity\User')->findBy(array(
                    'role' => 'CLIENT',
                    'idClient' => $ret['id'],
                ), array(
                    'lastLogin' => 'DESC'
                ));
                $ret['users'] = array();
                foreach ($users as $index => $user) {
                    $ret['users'][] = $user->prepareArray();
                }
            }
            return $ret;
        }
        else{
            return array();
        }
    }

    private function getGeneralStats(){
        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle\Entity\Client');
        return array(
            'all_logins_count' => $repo->findAllLoginsCount(),
            'all_offers_count' => $repo->findAllOffersCount(),
        );
    }
    private function metodsFixForPHPSTORM(){
        new Route(array());
        new Method(array());
    }
}