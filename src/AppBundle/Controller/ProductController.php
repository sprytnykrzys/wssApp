<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Controller\AppController as AppController;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


class ProductController extends FOSRestController
{
    use AppController;


    /**
     * @Route("/product")
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

        $id = isset($dataJSON['product']['id']) ? $dataJSON['product']['id'] : $request->get('id');

        return $this->updateProductAction($id);
    }

    /**
     * @Route("/product/{product_id}/")
     * @Method({"POST"})
     */
    public function updateProductAction($product_id = null){
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

        if(!is_null($product_id)){
            $product = $em->getRepository('AppBundle\Entity\Product')->find($product_id);
            if(!is_object($product)){
                $product = new Product();
                $new = true;
            }
        }
        else{
            $product = new Product();
            $new = true;
        }

        $code = isset($dataJSON['product']['code']) ? $dataJSON['product']['code'] : $request->get('code');
        $export_code = isset($dataJSON['product']['export_code']) ? $dataJSON['product']['export_code'] : $request->get('export_code');
        $price = isset($dataJSON['product']['price']) ? $dataJSON['product']['price'] : $request->get('price');
        $currency = isset($dataJSON['product']['currency']) ? $dataJSON['product']['currency'] : $request->get('currency');
        $measure_unit = isset($dataJSON['product']['measure_unit']) ? $dataJSON['product']['measure_unit'] : $request->get('measure_unit');
        //$creation_date = isset($dataJSON['product']['creation_date']) ? $dataJSON['product']['creation_date'] : $request->get('creation_date');

        if(!is_null($code)){
            $product->setCode($code);
        }
        else{
            if(!$product->getCode()){
                $this->errorPush( 'Code is required', 'code');
            }
        }

        if(!is_null($export_code)){
            $product->setExportCode($export_code);
        }
        else{
            if(!$product->getExportCode()){
                $this->errorPush( 'Export code is required', 'export_code');
            }
        }

        if(!is_null($price)){
            $product->setPrice($price);
        }
        else{
            if(!$product->getPrice()){
                $this->errorPush( 'Price is required', 'price');
            }
        }

        if(!is_null($currency)){
            $product->setCurrency($currency);
        }
        else{
            if(!$product->getCurrency()){
                $this->errorPush( 'Currency is required', 'currency');
            }
        }

        if(!is_null($measure_unit)){
            $product->setMeasureUnit($measure_unit);
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
            'product' => $this->prepareProductObjects($product),
            'message' => array(
                $new ? 'product added successfully' : 'product updated successfully'
            )
        ] , 200);
    }


    /**
     * @Route("/product")
     * @Method({"GET"})
     */
    public function getProductsAction()
    {
        $em = $this->getDoctrine()->getManager();
        $products = $em->getRepository('AppBundle\Entity\Product')->findAll();

        return $this->fastResponse([
            'success' => 1,
            'products' => $products
        ] , 200);
    }

    /**
     * @Route("/product/{product_id}/")
     * @Method({"GET"})
     */
    public function getProductAction($product_id)
    {
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle\Entity\Product')->find($product_id);

        return $this->fastResponse([
            'success' => 1,
            'product' => $this->prepareProductObject($product),
        ] , 200);
    }

    /**
     * @Route("/product")
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
     * @Route("/product/{product_id}/")
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
            $product = $em->getRepository('AppBundle\Entity\Product')->find($product_id);
            if(is_object($product)){
                $em->remove( $product );
                $em->flush();

                $this->response['success'] = 1;
                $this->response['message'] = 'Product with ID = '. $product_id .' has been removed';
                return $this->fastResponse($this->response, 200);
            }
            else{
                $this->response['hasError'] = 1;
                $this->response['message'] = 'Product with ID = '. $product_id .' doesn\'t exist';
                return $this->fastResponse($this->response, 400);
            }
        }
        $this->response['hasError'] = 1;
        $this->response['message'] = 'Product ID is null';
        return $this->fastResponse($this->response, 400);
    }

    /* Utilities */

    private function prepareProductObjects($arr){
        if(is_object($arr)){
            return $this->prepareProductObject($arr);
        }
        elseif (is_array($arr)){
            $products = array();
            foreach( $arr as $product ){
                $products[] = $this->prepareProductObject($product);
            }
            return $products;
        }
        else{
            return array();
        }
    }
    private function prepareProductObject($obj){
        if(is_object($obj)){
            return array(
                'id' => $obj->getId(),
                'code' => $obj->getCode(),
                'export_code' => $obj->getExportCode(),
                'price' => $obj->getPrice(),
                'currency' => $obj->getCurrency(),
                'measure_unit' => $obj->getMeasureUnit(),
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
