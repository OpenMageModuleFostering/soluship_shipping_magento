<?php
class Soluship_Shipping1_Model_Observer   
{
   public function __construct()
   {
   }
   public function automaticallyInvoiceShipCompleteOrder($observer)
   {
    $orderIds = $observer->getData('order_ids');
         Mage::log('createShipment.................... ');
       

        foreach($orderIds as $_orderId){




            $order     = Mage::getModel('sales/order')->load($_orderId);


             $carriers=Mage::getStoreConfig('carriers');

$converter=Mage::getModel('sales/convert_order');
$shipment=$converter->toShipment($order);


            $customer  = Mage::getModel('customer/customer')->load($order->getData('customer_id'));
            
            $obj=new Soluship_Shipping1_Model_Observer();

            $billingaddress1=$order->getBillingAddress();
            $billingaddress =    array( 'customerName'=>$order->getData('customer_firstname'),
                            'companyName'=>$billingaddress1->getData('company'),
                            'telephone'=>  $billingaddress1->getData('telephone'),
                            'email'=> $billingaddress1->getData('email'),
                            'street'=> $billingaddress1->getData('street'),
                            'city'=>  $billingaddress1->getData('city'),
                            'region'=> $billingaddress1->getData('region'),
                            'postcode'=> $billingaddress1->getData('postcode'),
                            'country'=> $billingaddress1->getData('country')
                            );

          $shipaddress=$shipment->getShippingAddress(); 



$ShippingAddress=array(  
                            'companyName'=>$shipaddress->getData('company'),
                            'telephone'=>  $shipaddress->getData('telephone'),
                            'email'=> $shipaddress->getData('email'),
                            'street'=> $shipaddress->getData('street'),
                            'city'=>  $shipaddress->getData('city'),
                            'region'=> $shipaddress->getData('region'),
                            'postcode'=> $shipaddress->getData('postcode'),
                            'country'=>  $shipaddress->getCountry()
                            );
 
$shipmethod=$order->getShippingMethod();


$ShippingMethod=array(  
                            'shipping'=>$order->getData('shipping_method')
                            
                            );



              $obj->biladd=$billingaddress;
              $obj->grandTotal=$order->getGrandTotal();
              $obj->shipping_method=$ShippingMethod;


             $obj->ShippingAddress=$ShippingAddress;
              $obj->id=$_orderId;
              $obj->shippingPackage=$this->createOrderItems($order);


              $obj->ship_setting = Mage::getStoreConfig('shipping'); 
 
$carriers=Mage::getStoreConfig('carriers');
$obj->soluship_setting=$carriers;
  
 $store_info=Mage::getStoreConfig('general');
 $obj->magento_store=$store_info;

$obj->currency=Mage::getStoreConfig('currency');
$obj->store_email=Mage::getStoreConfig('trans_email');

   

try {
      $client = new Varien_Http_Client($carriers['soluship_shipping1']['shippingserver'].'/api/v1/createShipment');
$client->setMethod(Varien_Http_Client::POST);
 $client->setHeaders('x-magento-shop-domain: MAGENTO');
/*$client->setHeaders('username: '.$carriers['soluship_shipping1']['apiusername']);
$client->setHeaders('password: '.$carriers['soluship_shipping1']['apipassword']);*/
 $client->setHeaders('SolushipAccessKey: '.$carriers['soluship_shipping1']['solushipaccesstoekn']);
 $client->setHeaders('SolushipHost: '.$_SERVER['SERVER_NAME']);

$json=json_encode($obj);
 
$client->setRawData($json, 'application/json');
  $response = $client->request();
} catch (Exception $e) {
     
}


   }

   return $this;
     
  }


   public function createOrderItems($order)
    {
      
            $items=$order->getAllVisibleItems();
            $i=0;
            $IO=array();
            $itemcount=count($items);
            $ww=0.0;

$storeId = Mage::app()->getStore()->getStoreId();

 

            foreach ($items as  $item)
            {
                    $IO[$i]['name']=$item->getName();
                    $IO[$i]['price']=$item->getPrice();
                    $IO[$i]['sku']=$item->getSku();
                    $IO[$i]['id']=$item->getProductId();
 

                    
                     //$cProduct = Mage::getModel('catalog/product');

                     $cProduct = Mage::getModel('catalog/product')->load($item->getProductId()); 
                  //$cProduct->load($item->getProductId());
                      $json1=  Mage::helper('core')->jsonEncode($cProduct);
 
                  $pweight=$cProduct->getWeight();
 
 
                    $IO[$i]['qty']=$item->getQtyOrdered(); 
                    $IO[$i]['weight']=$pweight;
                    $qty=$item->getQtyOrdered();
                    $ww = $ww + ((double)$pweight* (int)$qty);
                   
                    if($itemcount>1)
                    {
                            $IO[$i]['shipping']=$order->getData('shipping_amount')/$itemcount;
                            $IO[$i]['tax'] = $order->getData('tax_amount')/$itemcount;
                    }
                    elseif($itemcount == 1)
                    {
                            $IO[$i]['shipping']=$order->getData('shipping_amount');
                            $IO[$i]['tax'] = $order->getData('tax_amount');
                    }       
                    $i++;
            }
 




            $packs=array(  
                            'items'=>$IO,
                            'package_weight'=>$ww
                                                        
                            );




            return $packs;
    }
 

 public function cancelshipment($observer){


      $order = $observer->getEvent()->getOrder();

//$orderIds = (array)$this->getRequest()->getParam('order_ids');     
 
  $carriers=Mage::getStoreConfig('carriers');

  $json=  Mage::helper('core')->jsonEncode($order);

  Mage::log('<br>'.$json.'<br>');

  if($order->getStatusLabel()=='Canceled'){

  try {
      $client = new Varien_Http_Client($carriers['soluship_shipping1']['shippingserver'].'/api/v1/cancelshipment');
$client->setMethod(Varien_Http_Client::POST);
$client->setHeaders('x-magento-shop-domain: MAGENTO');
/*$client->setHeaders('username: '.$carriers['soluship_shipping1']['apiusername']);
$client->setHeaders('password: '.$carriers['soluship_shipping1']['apipassword']);*/
$client->setHeaders('SolushipAccessKey: '.$carriers['soluship_shipping1']['solushipaccesstoekn']);
$client->setHeaders('SolushipHost: '.$_SERVER['SERVER_NAME']);

 
$client->setRawData($json, 'application/json');
  $response = $client->request();
} catch (Exception $e) {
     
}

}


 }
/*
public function printlabel($observer)
    {
        $block = $observer->getEvent()->getBlock();
        if(get_class($block) =='Mage_Adminhtml_Block_Widget_Grid_Massaction'
            && $block->getRequest()->getControllerName() == 'sales_order')
        {
            $block->addItem('newmodule', array(
                'label' => 'New Mass Action Title',
                'url' => Mage::app()->getStore()->getUrl('newmodule/controller/action'),
            ));
        }
    }*/

/*
  private function getBillingAddress1($order){
echo "sachin";

    return $billingadd;

  }*/
}
?>