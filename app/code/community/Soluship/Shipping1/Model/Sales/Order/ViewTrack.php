<?php
class Soluship_Shipping1_Model_Sales_Order_View  extends Mage_Adminhtml_Block_Sales_Order_View	
{

	 public function  __construct() {

/*
 	$carriers=Mage::getStoreConfig('carriers');
	 		$url=$carriers['soluship_shipping1']['shippingserver']."/ecomPrintLabel.action?username=".$carriers['soluship_shipping1']['apiusername']."&password=".$carriers['soluship_shipping1']['apipassword']."&ids=".$this->getOrderId().",";
$tracking_url="";
	 		
$order=$this->getOrder();
  $json=  Mage::helper('core')->jsonEncode($order);

  Mage::log('<br>'.$json.'<br>');

  if($order->getStatusLabel()!='Canceled'){

  try {
      $client = new Varien_Http_Client($carriers['soluship_shipping1']['shippingserver'].'/api/v1/fulfillShipment');
$client->setMethod(Varien_Http_Client::POST);
 $client->setHeaders('x-magento-shop-domain: MAGENTO');
$client->setHeaders('username: '.$carriers['soluship_shipping1']['apiusername']);
$client->setHeaders('password: '.$carriers['soluship_shipping1']['apipassword']);

 
$client->setRawData($json, 'application/json');
  $response = $client->request();
 
   $resp=Mage::helper('core')->jsonDecode($response->getBody());

  $tracking_url=$resp['tracking_url'];



} catch (Exception $e) {
     
}



 
          $this->_addButton('tesddse', array(
            'label'     => Mage::helper('Sales')->__('Soluship Shipment Tracking'),
            'onclick'   => 'setLocation(\'' . $tracking_url  . '\')',
            'class'     => 'go'
        ), 0, 100, 'header', 'header');
}

*/
       
print_r("ddfdf");
        parent::__construct();

    }
}