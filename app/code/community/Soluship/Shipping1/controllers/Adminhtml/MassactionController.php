<?php
class Soluship_Shipping1_Adminhtml_MassactionController extends Mage_Adminhtml_Controller_Action
 {
    

     public function epayCaptureAction()
    {
        $orderIds = $this->getRequest()->getPost('order_ids', array());
		
		$remoteinterfaceError = false;
		
		$okIds = array();
		$ok_invoiceIds = array();
		$failIds = array();
		$notfoundIds = array();
		
		foreach ($orderIds as $orderId)
		{
			$order = Mage::getModel('sales/order')->load($orderId);


			 
 
//$orderIds = (array)$this->getRequest()->getParam('order_ids');     
 
  $carriers=Mage::getStoreConfig('carriers');

  $json=  Mage::helper('core')->jsonEncode($order);

  Mage::log('<br>'.$json.'<br>');

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

 /* $shipment = Mage::getModel('sales/order_shipment');
    $shipment->load($sc->getId());
    if($shipment->getId() != '') { 
        $track = Mage::getModel('sales/order_shipment_track')
                 ->setShipment($shipment)
                 ->setData('title', 'ShippingMethodName')
                 ->setData('number', $tracking_url)
                 ->setData('carrier_code', 'ShippingCarrierCode')
                 ->setData('order_id', $shipment->getData('order_id'))
                 ->save();
    }
*/

$shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
    ->setOrderFilter($order)
    ->load();

 print_r(Mage::helper('core')->jsonEncode($order));


} catch (Exception $e) {
     
}

}
			 
		}
			
		$ok = 'Order FullFillment Updated';
		 
		 
			$this->_getSession()->addSuccess($ok);
		 
		
		//$this->_redirect('adminhtml/sales_order/index');
    }
}