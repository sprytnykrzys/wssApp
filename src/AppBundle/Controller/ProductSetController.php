<?php

namespace AppBundle\Controller;

use AppBundle\Controller\AppController as AppController;
use AppBundle\Entity\ProductsSet;
use AppBundle\Helper\FileManager;
use FOS\RestBundle\Controller\FOSRestController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\ProductInSet;


class ProductSetController extends FOSRestController
{
    use AppController;

    /* CRUD common method for product in set management services */
    public function manageProductInSet($id_product = null, $id_set = null, $quantity = null){
        $em = $this->getDoctrine()->getManager();

        if(is_null($quantity)){
            $quantity = 1;
        }
        $quantity = (int)$quantity;
        if(is_null($id_product) || is_null($id_set)){
            return $this->fastResponse(array(
                'errors' => 'id_product and id_set are required',
            ));
        }

        $product = $em->getRepository('AppBundle\Entity\Product')->find($id_product);
        $set = $em->getRepository('AppBundle\Entity\ProductsSet')->find($id_set);

        if(!is_object($product) || !is_object($set)){
            return $this->fastResponse(array(
                'errors' => 'id_product or id_set doesn\'t exist',
            ));
        }

        $productInSet = $em->getRepository('AppBundle\Entity\ProductInSet')->find(array(
                'set' => $set->getId(),
                'product' => $product->getId()
            )
        );

        if(!is_object($productInSet)){
            if($quantity > 0){
                $productInSet = new ProductInSet($set, $product, $quantity);
                $em->persist( $productInSet );
            }
            else{
                return $this->fastResponse(array(
                    'errors' => 'nothing changed - product is currently not associated with set',
                ));
            }
        }
        else{
            $currentQuantity = (int)$productInSet->getQuantity();
            if( ($currentQuantity + $quantity) > 0 ){
                $productInSet->setQuantity( $currentQuantity + $quantity );
            }
            else{
                $em->remove( $productInSet );
            }
        }

        $em->flush();

        return $this->fastResponse(array(
            'success' => 1,
            'product_in_set' => $productInSet
        ));
    }

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
     * @Route("/products_set/remove_product/")
     * @Method({"POST"})
     *
     * @deprecated
     */
    public function removeProductFromSetAction()
    {
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }

        $em = $this->getDoctrine()->getManager();
        $request = Request::createFromGlobals();

        $dataJSON = $this->getJSONRequest();

        $products_set_id = isset($dataJSON['products_set']['id_set']) ? $dataJSON['products_set']['id_set'] : $request->get('id_set');
        $id_product = isset($dataJSON['products_set']['id_product']) ? $dataJSON['products_set']['id_product'] : $request->get('id_product');

        if(is_null($products_set_id)){
            return $this->fastResponse(['message' => array('ID Products set is null') ] , 400);
        }
        if(is_null($id_product)){
            return $this->fastResponse(['message' => array('ID Product is null') ] , 400);
        }
        $product_set = $em->getRepository('AppBundle\Entity\ProductsSet')->find($products_set_id);
        $product = $em->getRepository('AppBundle\Entity\Product')->find($id_product);

        if(!is_object($product_set)){
            return $this->fastResponse(['message' => array('Products set doesn\'t exist') ] , 400);
        }
        if(!is_object($product)){
            return $this->fastResponse(['message' => array('Product doesn\'t exist') ] , 400);
        }
        //$product_set->unsetProduct($product);
        $productInSet = $em->getRepository('AppBundle\Entity\ProductInSet')->find(array(
                'set' => $product_set->getId(),
                'product' => $product->getId()
            )
        );

        if(is_object($productInSet)){

            $em->remove( $productInSet );
            $em->flush();

            return $this->fastResponse([
                'message' => array('Successfully removed from set')
            ] , 200);
        }
        return $this->fastResponse(array(
            'errors' => 'nothing changed - product is currently not associated with set',
        ));
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
        $pathToImages = $this->webDir.ProductsSet::$imagePath;

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

        $name = isset($dataJSON['products_set']['name']) ? $dataJSON['products_set']['name'] : $request->get('name');
        $export_code = isset($dataJSON['products_set']['export_code']) ? $dataJSON['products_set']['export_code'] : $request->get('export_code');
        $number = isset($dataJSON['products_set']['code']) ? $dataJSON['products_set']['code'] : $request->get('code');
        $type = isset($dataJSON['products_set']['type']) ? $dataJSON['products_set']['type'] : $request->get('type');
        $id_system = isset($dataJSON['products_set']['id_system']) ? $dataJSON['products_set']['id_system'] : $request->get('id_system');
        $products = isset($dataJSON['products_set']['products']) ? $dataJSON['products_set']['products'] : $request->get('products');
        $image = isset($dataJSON['products_set']['image']) ? $dataJSON['products_set']['image'] : $request->get('image');
        //$creation_date = isset($dataJSON['product']['creation_date']) ? $dataJSON['product']['creation_date'] : $request->get('creation_date');

        if(!is_null($name)){
            $product->setName($name);
        }
        else{
            if(!$product->getName()){
                $this->errorPush( 'Name is required', 'name');
            }
        }

        if(!is_null($number)){
            $product->setNumber($number);
        }
        else{
            if(!$product->getNumber()){
                $this->errorPush( 'Number is required', 'number');
            }
        }

        if(!is_null($export_code)){
            $product->setExportCode($export_code);
        }
//        else{
//            if(!$product->getExportCode()){
//                $this->errorPush( 'Export code is required', 'export_code');
//            }
//        }


        if(!is_null($type)){
            $product->setType($type);
        }
        else{
            if(!$product->getType()){
                $this->errorPush( 'Type is required', 'type');
            }
        }

//        if(!is_null($id_system)){
//            $product->setIdSystem($id_system);
//        }
//        else{
//            if(!$product->getIdSystem()){
//                $this->errorPush( 'id_system is required', 'id_system');
//            }
//        }

        if(!is_null($id_system) && (int)$id_system){
            $system = $em->getRepository('AppBundle\Entity\Hierarchy')->find($id_system);
            if(is_object($system) && $system->getLevel() == 2){
                $product->setHierarchy($system);
            }
            else{
                $this->errorPush( 'System doesn\'t exist', 'id_system');
            }
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

        if(is_array($products)){
            $coll = $em->getRepository('AppBundle\Entity\Product')->findByIds( $products );
            foreach ($coll as $prod){
                $this->manageProductInSet( $prod->getId(), $product->getId(), 1);
            }
        }

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
//            'product' => $this->prepareProductsSetObjects($product),
//            'coll' => $coll,
            'message' => array(
                $new ? 'products set added successfully' : 'products set updated successfully'
            )
        ] , 200);
    }

    /**
     * @Route("/products_set/{id_set}/product/{id_product}/{quantity}/")
     * @Method({"POST"})
     */
    public function restAddProductToSetAction($id_set, $id_product, $quantity){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }
        return $this->manageProductInSet($id_product, $id_set, $quantity);
    }

    /**
     * @Route("/products_set/{id_set}/product/{id_product}/{quantity}/delete/")
     * @Method({"POST"})
     */
    public function restSubstractProductFromSetAction($id_set, $id_product, $quantity){
        if( !$this->authenticate()){
            return $this->prepareAuthRequiredResponse();
        }
        if(!$this->isAdmin()){
            return $this->tooFewPrivilegesResponse();
        }
        return $this->manageProductInSet($id_product, $id_set, -$quantity);
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
            'products_sets' => $this->prepareProductsSetObjects($products)
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

                foreach($product->getProducts() as $productInSet){
                    $this->manageProductInSet($productInSet->getProduct()->getId(), $product->getId(), -(int)$productInSet->getQuantity());
                }
                //return $this->fastResponse(array('set' => $product), 200);
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
