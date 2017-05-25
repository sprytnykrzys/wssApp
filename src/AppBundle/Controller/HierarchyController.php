<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hierarchy;
use AppBundle\Controller\AppController;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class HierarchyController extends FOSRestController
{
    use AppController;
    const KEY = 'rmuwt6546wel4t65';
    const levels = [
        '0' => 'catalog',
        '1' => 'system_provider',
        '2' => 'system'
    ];

    const levelsPlural = [
        '0' => 'catalogs',
        '1' => 'system_providers',
        '2' => 'systems'
    ];


    /**
     * @Route("/hierarchy")
     * @Method({"POST"})
     */
    public function postHierarchySingleAction(){
        return $this->fastResponse(array(
            'message' => 'not implemented yet'
        ), 418); /* I am a teapot ;-) */
    }

    /**
     * @Route("/hierarchy/{level}/{id_hierarchy}/")
     * @Method({"POST"})
     */
    public function postHierarchyAction($id_hierarchy = null, $level = 0){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }
        $new = false;
        $em = $this->getDoctrine()->getManager();
        $dataJSON = $this->getJSONRequest();

        $hierarchyName = self::levels[$level];
        if(!isset($dataJSON[$hierarchyName])){
            /* general fallback */
            $hierarchyName = 'hierarchy';
        }
        $name = isset($dataJSON[$hierarchyName]['name']) ? $dataJSON[$hierarchyName]['name'] : $this->request->get('name');
        $id_parent = isset($dataJSON[$hierarchyName]['id_parent']) ? $dataJSON[$hierarchyName]['id_parent'] : $this->request->get('id_parent');
//        return $this->fastResponse(array(
//            'hn' => $hierarchyName,
//            'name' => $name,
//            'idp' => $id_parent
//        ));

        if(is_null($id_hierarchy)){
            $id_hierarchy = isset($dataJSON[$hierarchyName]['id']) ? $dataJSON[$hierarchyName]['id'] : $this->request->get('id');
        }
        $hierarchy = $em->getRepository('AppBundle\Entity\Hierarchy')->find($id_hierarchy);
        if(!is_object($hierarchy)){
            $hierarchy = new Hierarchy();
            $new = true;
        }

        if(!is_null($name)){
            $hierarchy->setName($name);
        }
        else{
            if(!$hierarchy->getName()){
                $this->errorPush( 'Name is required', 'name');
            }
        }

        if(!is_null($id_parent)){
            $hierarchy->setIdParent($id_parent);
        }
        else{
            if(is_null($hierarchy->getIdParent())){
                $this->errorPush( 'ID parent must be provided', 'id_parent');
            }
        }

        if($new){
            $dateNow = new \DateTime("now");
            $hierarchy->setCreationDate($dateNow);
            /* prevent accidentally level change */
            if(!is_null($level)){
                $hierarchy->setLevel($level);
            }
            else{
                if(!$hierarchy->getLevel()){
                    $this->errorPush( 'Level is required', 'level');
                }
            }
        }

        if($this->hasErrors()){
            return $this->fastResponse([
                'hasErrors' => 1,
                'name' => $name,
                'id_parent' => $id_parent
            ] , 400);
        }

        $em->persist( $hierarchy );
        $em->flush();


        return $this->fastResponse(array(
            'success' => 1,
            $hierarchyName => $this->prepareHierarchyObjects($hierarchy),
            'message' => array(
                $new ? $hierarchyName . ' added successfully' : $hierarchyName . ' updated successfully'
            )
        ));
    }

    /**
     * @Route("/catalog")
     * @Method({"POST"})
     */
    public function catalogAddAction(){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }



        return $this->fastResponse([
            'success' => 1,
            //'WSS' => $this->prepareHierarchyObjects( $object ),
            'message' => array(
                'client added successfully'
            )
        ] , 200);
    }
    /**
     * @Route("/catalog/{id}/")
     * @Method({"POST"})
     */
    public function updateAction($id = null){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }
        return $this->fastResponse([
            'success' => 1,
            'client' => $this->prepareHierarchyObject($client),
            'message' => array(
                'client added successfully'
            )
        ] , 200);
    }

    /**
     * @Route("/catalog")
     * @Method({"GET"})
     */
    public function getCatalogAction(){
        return $this->getCatalogByIdAction();
    }

    /**
     * @Route("/catalog/{id}/")
     * @Method({"GET"})
     */
    public function getCatalogByIdAction($id = 0){

        $request = Request::createFromGlobals();
        $dataJSON = $this->getJSONRequest();

        $em = $this->getDoctrine()->getManager();
        $repo = $em->getRepository('AppBundle\Entity\Hierarchy');
        $hierarchy = $repo->findAll();
        $catalog = $this->prepareHierarchyObjects($hierarchy);

        return $this->fastResponse(array(
            'catalog' => $repo->findAllNested($catalog, $id)
        ), 200);

    }
    /**
     * @Route("/catalog")
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
     * @Route("/catalog/{id}/")
     * @Method({"DELETE"})
     */
    public function deleteAction($id = null){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }

        $this->response['success'] = 1;
        $this->response['message'] = 'ok';
        return $this->fastResponse($this->response, 200);
    }
    private function prepareHierarchyObjects($arr){
        if(is_object($arr)){
            return $this->prepareHierarchyObject($arr);
        }
        elseif (is_array($arr)){
            $catalog = array();
            foreach( $arr as $node ){
                $catalog[] = $this->prepareHierarchyObject($node);
            }
            return $catalog;
        }
        else{
            return array();
        }
    }

    private function prepareHierarchyObject($obj){
        if(is_object($obj)){
            return array(
                'id' => $obj->getId(),
                'id_parent' => $obj->getIdParent(),
                'name' => $obj->getName(),
                'creation_date' => $obj->getCreationDate(),
            );
        }
        else{
            return array();
        }
    }
}