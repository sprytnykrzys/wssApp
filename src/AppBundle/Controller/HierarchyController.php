<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Hierarchy;
use FOS\RestBundle\Controller\FOSRestController;
use Symfony\Component\HttpFoundation\Request;

class HierarchyController extends FOSRestController
{
    use AppController;
    const KEY = 'rmuwt6546wel4t65';
    const CATALOG_LEVEL = 0;
    const SYSTEM_PROVIDER_LEVEL = 1;
    const SYSTEM_LEVEL = 2;
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

        if(is_null($id_hierarchy)){
            $id_hierarchy = isset($dataJSON[$hierarchyName]['id']) ? $dataJSON[$hierarchyName]['id'] : $this->request->get('id');
        }
        if(is_null($id_hierarchy)){
            $hierarchy = new Hierarchy();
            $new = true;
        }
        else{
            $hierarchy = $em->getRepository('AppBundle\Entity\Hierarchy')->find($id_hierarchy);
            if(!is_object($hierarchy)){
                $hierarchy = new Hierarchy();
                $new = true;
            }
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
            if($id_parent != 0 && !$this->nodeExist($id_parent)){
                $this->errorPush( 'Parent with given ID doesn\'t exist', 'id_parent');
            }
            else{
                if($id_parent != 0 ){
                    $parent = $em->getRepository('AppBundle\Entity\Hierarchy')->find($id_parent);
                    $parLevel = $parent->getLevel();
                    if(($parLevel + 1) == $level){
                        $hierarchy->setIdParent($id_parent);
                    }
                    else{
                        $this->errorPush( 'Hierarchy error - '. self::levels[$parLevel] . ' cannot be parent of ' . self::levels[$level] , 'id_parent');
                    }
                }
                else{
                    $hierarchy->setIdParent($id_parent);
                }

            }
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
                'hasErrors' => 1
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
     * @Route("/hierarchy/{level}/{id}/")
     * @Method({"DELETE"})
     */
    public function deleteAction($id = null, $level){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }
        $em = $this->getDoctrine()->getManager();
        /* rubbish to REMOVE */
        $repo = $em->getRepository('AppBundle\Entity\Hierarchy');

        $hierarchy = $repo->findOneBy(array(
            'id' => $id,
            'level' => $level
        ));

        if(!is_object($hierarchy) || (is_object($hierarchy) && $hierarchy->getLevel() != $level)){
            $this->response['hasError'] = 1;
            $this->response['errors'] = 'Wrong hierarchy';
            return $this->fastResponse($this->response, 400);
        }

        $all = $repo->findAll();
        $allObjectsByIds = array();
        foreach ($all as $item) {
            $allObjectsByIds[ $item->getId() ] = $item;
        }

        $hierarchyArray = $this->prepareHierarchyObject($hierarchy);
        $allArray = $this->prepareHierarchyObjects($all);
        $allArray = $repo->findAllNested($allArray, $hierarchy->getId());

        $empty = array();
        $errors = array();

        foreach ($allArray as $key => $node){
            if(
                empty($node['products']) &&
                empty($node['products_sets'])
            ){
                $empty[] = $allObjectsByIds[$node['id']];
            }
            else{
                $errors = self::levels[ $node['level'] ] . ' ' .  $node['id'] . ' is not empty';
            }
        }
        if(is_array($hierarchyArray) && empty($hierarchyArray['products']) && empty($hierarchyArray['products_sets'])){
            $empty[] = $hierarchy;
        }
        else if(is_array($hierarchyArray)){
            $errors = self::levels[ $hierarchyArray['level'] ] . ' ' .  $hierarchyArray['id'] . ' is not empty';
        }

//        return $this->fastResponse(array(
//            'hier' => $hierarchyArray,
//            'empty' => $empty,
//            'errors' => $errors,
//            'children' => $allArray,
//            'all' => $all,
//        ));
        /* TO REMOVE */

        if(empty($errors)){
            foreach($empty as $hierarchySingle){
                $em->remove( $hierarchySingle );
            }
            $em->flush();

            $this->response['success'] = 1;
            $this->response['message'] = ucfirst(self::levels[$level]) . ' with ID = '. $id .' has been removed';
            return $this->fastResponse($this->response, 200);

        }

        $this->response['hasError'] = 1;
        $this->response['errors'] = $errors;
        return $this->fastResponse($this->response, 400);
    }
    private function deleteRecursive($hierarchy){

    }
    /* Catalog services set */
    /**
     * @Route("/catalog")
     * @Method({"POST"})
     */
    public function catalogAddAction(){
        return $this->postHierarchyAction(null, self::CATALOG_LEVEL);
    }

    /**
     * @Route("/catalog/{id}/")
     * @Method({"POST"})
     */
    public function catalogUpdateAction($id = null){
        return $this->postHierarchyAction($id, self::CATALOG_LEVEL);
    }

    /**
     * @Route("/catalog/{id}/delete/")
     * @Method({"POST"})
     */
    public function catalogDeleteAction($id = null){
        return $this->fastResponse(array(
            'error' => 'not implemented'
        ), 418);
        // return $this->deleteAction($id, self::CATALOG_LEVEL);
    }

    /**
     * @Route("/system_provider/{id}/")
     * @Method({"POST"})
     */
    public function systemProviderUpdateAction($id = null){
        return $this->postHierarchyAction($id, self::SYSTEM_PROVIDER_LEVEL);
    }

    /* System provider services set */
    /**
     * @Route("/system_provider")
     * @Method({"POST"})
     */
    public function systemProviderAddAction(){
        return $this->postHierarchyAction(null, self::SYSTEM_PROVIDER_LEVEL);
    }

    /**
     * @Route("/system_provider/{id}/delete/")
     * @Method({"POST"})
     */
    public function systemProviderDeleteAction($id = null){
        return $this->deleteAction($id, self::SYSTEM_PROVIDER_LEVEL);
    }

    /* System services set */
    /**
     * @Route("/system")
     * @Method({"POST"})
     */
    public function systemAddAction(){
        return $this->postHierarchyAction(null, self::SYSTEM_LEVEL);
    }

    /**
     * @Route("/system/{id}/")
     * @Method({"POST"})
     */
    public function systemUpdateAction($id = null){
        return $this->postHierarchyAction($id, self::SYSTEM_LEVEL);
    }

    /**
     * @Route("/system/{id}/delete/")
     * @Method({"POST"})
     */
    public function systemDeleteAction($id = null){
        return $this->deleteAction($id, self::SYSTEM_LEVEL);
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
        $unassigned = array();
        return $this->fastResponse(array(
            'catalog' => $repo->findAllNested($catalog, $id, $unassigned),
            'unassigned' => $unassigned
        ), 200);

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
                'level' => $obj->getLevel(),
                'products' => $obj->getProductsIds(),
                'products_sets' => $obj->getProductsSetsIds()
            );
        }
        else{
            return array();
        }
    }

    private function nodeExist($id, $repo = null){
        if(is_null($repo)){
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('AppBundle\Entity\Hierarchy');
        }
        $hierarchy = $repo->find($id);
        return is_object($hierarchy);
    }
}