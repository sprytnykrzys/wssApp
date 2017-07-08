<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AppController as AppController;
use AppBundle\Entity\Product;
use AppBundle\Helper\FileManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
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
        $pathToImages = $this->webDir.Product::$imagePath;

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
        $name = isset($dataJSON['product']['name']) ? $dataJSON['product']['name'] : $request->get('name');
        $id_system = isset($dataJSON['product']['id_system']) ? $dataJSON['product']['id_system'] : $request->get('id_system');
        $image = isset($dataJSON['product']['image']) ? $dataJSON['product']['image'] : $this->request->get('image');
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

        if(!is_null($name)){
            $product->setName($name);
        }
        else{
            if(!$product->getName()){
                $this->errorPush( 'Name is required', 'name');
            }
        }

        if(!is_null($id_system) && (int)$id_system){
            $system = $em->getRepository('AppBundle\Entity\Hierarchy')->find($id_system);
            if(is_object($system) && $system->getLevel() == 2){
                $product->setHierarchy($system);
            }
            else{
                $this->errorPush( 'System doesn\'t exist required', 'id_system');
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

        if(!is_null($image)){
            $image = new FileManager( $pathToImages, $image );
            if($image != false){
                $fileName = $image->save( $product->getId() );
                $product->setImage(  $fileName );
                $em->persist($product);
                $em->flush();
            }
        }

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
            'products' => $this->prepareProductObjects($products),
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
     * @Route("/product/delete")
     * @Method({"DELETE"})
     */
    public function deleteGetPostAction(){
        return $this->deleteGetAction();
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
     * @Route("/product/{product_id}/delete/")
     * @Method({"POST"})
     */
    public function deletePostAction($product_id = null){
        return $this->deleteAction($product_id);
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
        if(is_null($product_id)){
            $this->response['hasError'] = 1;
            $this->response['message'] = 'Product ID is null';
            return $this->fastResponse($this->response, 400);
        }
        $em = $this->getDoctrine()->getManager();
        $product = $em->getRepository('AppBundle\Entity\Product')->find($product_id);

        if(!is_object($product)){
            $this->response['hasError'] = 1;
            $this->response['message'] = 'Product with ID = '. $product_id .' doesn\'t exist';
            return $this->fastResponse($this->response, 400);
        }
        if($existsConstraints = $this->checkConstraints($product, $em)){
            return $existsConstraints;
        }
        $em->remove( $product );
        $em->flush();

        $this->response['success'] = 1;
        $this->response['message'] = 'Product with ID = '. $product_id .' has been removed';
        return $this->fastResponse($this->response, 200);


    }

    private function checkConstraints($product, $em){
        $request = Request::createFromGlobals();
        $dataJSON = $this->getJSONRequest();

        $force = isset($dataJSON['force']) ? $dataJSON['force'] : $request->get('force');
        $inSet = $em->getRepository('AppBundle\Entity\ProductInSet')->findBy(array(
            'product' => $product->getId()
        ));
        if(empty($inSet)){
            return false;
        }
        if($force){
            foreach ($inSet as $singleInSet){
                $em->remove( $singleInSet );
            }
            $em->flush();
            return false;
        }
        $this->response['hasError'] = 1;
        $this->response['message'] = 'Product belongs to sets. If you want to remove product from all sets send request again with parameter force=1 parameter';
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
            return $obj->prepareArray();
        }
        else if(is_array($obj)){
            return $obj;
        }
        else{
            return array();
        }
    }
    private function metodsFixForPHPSTORM(){
        new Route(array());
        new Method(array());
    }
}
