<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductsSet;
use AppBundle\Controller\AppController as AppController;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


class ProductSetController extends FOSRestController
{
    use AppController;


    /**
     * @Route("/products_set")
     * @Method({"POST"})
     */
    public function addProductAction()
    {
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }

        $request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        $id = isset($dataJSON['products_set']['id']) ? $dataJSON['products_set']['id'] : $request->get('id');

        return $this->updateProductAction($id);
    }

    /**
     * @Route("/products_set/{products_set_id}/")
     * @Method({"POST"})
     */
    public function updateProductAction($products_set_id = null){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }

        $new = false;
        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        if(!is_null($products_set_id)){
            $product = $em->getRepository('AppBundle\Entity\ProductsSet')->find($products_set_id);
            if(!is_object($product)){
                $product = new ProductsSet();
                $new = true;
            }
        }
        else{
            $product = new ProductsSet();
            $new = true;
        }

        $number = isset($dataJSON['products_set']['number']) ? $dataJSON['products_set']['number'] : $request->get('number');
        $type = isset($dataJSON['products_set']['type']) ? $dataJSON['products_set']['type'] : $request->get('type');
        $id_system = isset($dataJSON['products_set']['id_system']) ? $dataJSON['products_set']['id_system'] : $request->get('id_system');
        $products = isset($dataJSON['products_set']['products']) ? $dataJSON['products_set']['products'] : $request->get('products');
        //$creation_date = isset($dataJSON['product']['creation_date']) ? $dataJSON['product']['creation_date'] : $request->get('creation_date');



        if(!is_null($number)){
            $product->setNumber($number);
        }
        else{
            if(!$product->getNumber()){
                $this->errorPush( 'Number is required', 'number');
            }
        }

        if(!is_null($type)){
            $product->setType($type);
        }
        else{
            if(!$product->getType()){
                $this->errorPush( 'Type is required', 'type');
            }
        }

        if(!is_null($id_system)){
            $product->setIdSystem($id_system);
        }
        else{
            if(!$product->getIdSystem()){
                $this->errorPush( 'id_system is required', 'id_system');
            }
        }

        $coll = array();
        if(is_array($products)){
            $coll = $em->getRepository('AppBundle\Entity\Product')->findByIds( $products );
            $product->setProducts($coll);
        }


        if($new){
            $dateNow = new \DateTime("now");
            $product->setCreationDate($dateNow);
        }

        if($this->hasErrors()){
            return $this->fastResponse([
                'hasErrors' => 1,
            ] , 400);
        }

        $em->persist( $product );
        $em->flush();

        return $this->fastResponse([
            'success' => 1,
            'product' => $this->prepareProductsSetObjects($product),
            'coll' => $coll,
            'message' => array(
                $new ? 'product added successfully' : 'product updated successfully'
            )
        ] , 200);
    }


    /**
     * @Route("/products_set")
     * @Method({"GET"})
     */
    public function getProductsSetAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle\Entity\ProductsSet')->findAll();

        return $this->fastResponse([
            'success' => 1,
            'products_sets' => $products
        ] , 200);
    }

    /**
     * @Route("/products_set/{product_id}/")
     * @Method({"GET"})
     */
    public function getProductAction($products_set_id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle\Entity\ProductsSet')->find($products_set_id);

        return $this->fastResponse([
            'success' => 1,
            'products_set' => $this->prepareProductsSetObject($product),
        ] , 200);
    }

    /**
     * @Route("/products_set/delete")
     * @Method({"DELETE"})
     */
    public function deleteGetPostAction(){
        return $this->deleteGetAction();
    }

    /**
     * @Route("/products_set")
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
     * @Route("/products_set/{product_id}/delete/")
     * @Method({"POST"})
     */
    public function deletePostAction($product_id = null){
        return $this->deleteAction($product_id);
    }
    /**
     * @Route("/products_set/{product_id}/")
     * @Method({"DELETE"})
     */
    public function deleteAction($product_id = null){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }
        if(!is_null($product_id)){
            $em = $this->getDoctrine()->getManager();
            $product = $em->getRepository('AppBundle\Entity\ProductsSet')->find($product_id);
            if(is_object($product)){
                $em->remove( $product );
                $em->flush();

                $this->response['success'] = 1;
                $this->response['message'] = 'Products set with ID = '. $product_id .' has been removed';
                return $this->fastResponse($this->response, 200);
            }
            else{
                $this->response['hasError'] = 1;
                $this->response['message'] = 'Products set with ID = '. $product_id .' doesn\'t exist';
                return $this->fastResponse($this->response, 400);
            }
        }
        $this->response['hasError'] = 1;
        $this->response['message'] = 'Products set ID is null';
        return $this->fastResponse($this->response, 400);
    }

    /* Utilities */

    private function prepareProductsSetObjects($arr){
        if(is_object($arr)){
            return $this->prepareProductsSetObject($arr);
        }
        elseif (is_array($arr)){
            $products = array();
            foreach( $arr as $product ){
                $products[] = $this->prepareProductsSetObject($product);
            }
            return $products;
        }
        else{
            return array();
        }
    }
    private function prepareProductsSetObject($obj){
        if(is_object($obj)){
            return array(
                'id' => $obj->getId(),
                'type' => $obj->getType(),
                'number' => $obj->getNumber(),
                'id_system' => $obj->getIdSystem(),
                'products' => $obj->getProducts()->toArray(),
                'creation_date' => $obj->getCreationDate(),
            );
        }
        else if(is_array($obj)){
            return $obj;
        }
        else{
            return array();
        }
    }

}
