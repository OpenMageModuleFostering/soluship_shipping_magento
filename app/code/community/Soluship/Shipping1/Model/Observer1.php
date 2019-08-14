<?php

/*
 * Author: Hashid Hameed
 * Email: me@hashid.in
 * Date: 16 Oct 2014
*/

class Soluship_Shipping1_Model_Observer1 
{


   /*  public function addMassAction(Varien_Event_Observer $observer) {
        $block = $observer->getEvent()->getBlock();

        if (
            $block instanceof Mage_Adminhtml_Block_Widget_Grid_Massaction
            && $block->getRequest()->getControllerName() == 'sales_order'
        ) {

         
            
             $block->addItem('keyup_massshipment_no_emails', array(
                 'label' => 'FullFill Shipping',
            'url' =>  Mage::app()->getStore()->getUrl('shipping1/adminhtml_massaction/epayCapture'),
            )); 

            
        }
    }*/

    
    public function trackUpdate(Varien_Event_Observer $observer){

      $block = $observer->getEvent()->getData( 'block' );
//
     if ($block instanceof Mage_Adminhtml_Block_Sales_Order_View) {

$tracking_url="";
$carriers=Mage::getStoreConfig('carriers');

     $order=$block->getOrder();

      $json=  Mage::helper('core')->jsonEncode($order);


      if($order->getStatusLabel()!='Canceled'){

  try {
      $client = new Varien_Http_Client($carriers['soluship_shipping1']['shippingserver'].'/api/v1/fulfillShipment');
$client->setMethod(Varien_Http_Client::POST);
 $client->setHeaders('x-magento-shop-domain: MAGENTO');
/*$client->setHeaders('username: '.$carriers['soluship_shipping1']['apiusername']);
$client->setHeaders('password: '.$carriers['soluship_shipping1']['apipassword']);*/
$client->setHeaders('SolushipAccessKey: '.$carriers['soluship_shipping1']['solushipaccesstoekn']);
$client->setHeaders('SolushipHost: '.$_SERVER['SERVER_NAME']);

 
$client->setRawData($json, 'application/json');
  $response = $client->request();
 
   $resp=Mage::helper('core')->jsonDecode($response->getBody());

  $tracking_url=$resp['tracking_url'];



} catch (Exception $e) {
     
}
}

if($tracking_url!=""){

$block->addButton('do_something_crazy', array(
                'label'     => 'Soluship  Shipment Tracking',
                'onclick'   => 'popWin(\'' . $tracking_url . '\')',
                'class'     => 'go'
            )); 
 $url11=$carriers['soluship_shipping1']['shippingserver']."/ecomPrintLabel.action?SolushipAccessKey=".$carriers['soluship_shipping1']['solushipaccesstoekn']."&ids=".$order->entity_id."&ecomplatform=magento";

$block->addButton('do_something_craz1y', array(
                'label'     => 'Soluship Print Label',
                'onclick'   => 'popWin(\'' . $url11 . '\')',
                'class'     => 'go'
            )); 


     
/*
     $block->addButton('solushippl', array(
            'label'     => Mage::helper('Sales')->__('Soluship Print Label'),
            'onclick'   => 'setLocation(\'' . $url . '\')',
            'class'     => 'go'
        ), 0, 100, 'header', 'header');*/


 



}


  $url1=$carriers['soluship_shipping1']['shippingserver']."/ecomOrderRepeat.action?SolushipAccessKey=".$carriers['soluship_shipping1']['solushipaccesstoekn']."&ids=".$order->entity_id.",&ecomplatform=magento";
  /*$url1=$carriers['soluship_shipping1']['shippingserver']."/ecomOrderRepeat.action?ids=".$order->entity_id.",&ecomplatform=magento";*/



$block->addButton('do_something_crazy1', array(
                'label'     => 'Recreate / Soluship Shipping Status',
                'onclick'   => 'popWin(\'' . $url1 . '\')',
                'class'     => 'go'
            )); 

 if($order->getStatusLabel()!="Canceled"){


 $message="Do want to Create Shipping Order in Soluship ?.";
 $block->addButton('in_shipment', 
            array( 'label' => Mage::helper('sales')->__('Create Shipping Order'), 
                'onclick' => "popWin(confirmSetLocation('{$message}', '{$block->getUrl('shipping1/adminhtml_index/shipment')}'))", 'class' => 'go' ));  
 
               }        
        }
    }
 
} 