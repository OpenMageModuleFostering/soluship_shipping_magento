<?php
 class Soluship_Shipping1_Model_Demo
extends Mage_Shipping_Model_Carrier_Abstract
implements Mage_Shipping_Model_Carrier_Interface
{
  protected $_code = 'soluship_shipping1';
 
  public function collectRates(Mage_Shipping_Model_Rate_Request $request)
  {
    $result = Mage::getModel('shipping/rate_result');
/*  $result->append($this->_getDefaultRate());
    $result->append($this->_getDefaultRate1());

*/
//http://localhost:9090/solushipRates/rest/soluship/woogetRates
Mage::log('getnerice rates.................... ');
 $carriers=Mage::getStoreConfig('carriers');
 

$cart = Mage::getModel('checkout/cart')->getQuote();

$client = new Varien_Http_Client($carriers['soluship_shipping1']['shippingserver'].'/api/v1/genericrates',
 array(
    'maxredirects' => 0,
    'timeout'      => 20));

$client->setMethod(Varien_Http_Client::POST);
$request->ship_setting = Mage::getStoreConfig('shipping'); 
$client->setHeaders('x-magento-shop-domain: MAGENTO');
/*$client->setHeaders('username: '.$carriers['soluship_shipping1']['apiusername']);
$client->setHeaders('password: '.$carriers['soluship_shipping1']['apipassword']);*/
$client->setHeaders('SolushipAccessKey: '.$carriers['soluship_shipping1']['solushipaccesstoekn']);
$client->setHeaders('SolushipHost: '.$_SERVER['SERVER_NAME']);
$weight_unit=$this->getConfigData('unit_of_measure');
$result->customer = Mage::getSingleton('customer/session')->getCustomer();
$store_info=Mage::getStoreConfig('general');
 $request->magento_store=$store_info;
$request->currency=Mage::getStoreConfig('currency');
$request->store_email=Mage::getStoreConfig('trans_email');
$request->soluship_setting=Mage::getStoreConfig('carriers');

$request->cartItems =  Mage::getSingleton('checkout/session')->getQuote();  
$new = array();
 
$session = Mage::getSingleton('checkout/session');
$i=1;
foreach ($session->getQuote()->getAllItems() as $item) {

  $new[$i]=$item->getData();
$i=$i+1;
    
}
$request->items=$new;
$config = array();
$json =json_encode($config);
$request->weight_unit=$weight_unit;
$json = Mage::helper('core')->jsonEncode($request);
$client->setRawData($json, 'application/json');
 
// print_r(json_encode(Mage::getStoreConfig('carriers')));

//more parameters
try{
     $response = $client->request();
     $jsond=json_decode($response->getBody());
 //echo $response;
 //print_r($jsond);
 
 
    $ratebody =$jsond;
                    $obj      = $ratebody;
                    $last     = count($obj->rates);
                    foreach ($obj->rates as $key => $value) {
                        $serviceId = 0;
                        $service   = '';
                        $amount    = 0.0;
                        $carrier='';
                        $carrierId=0;
                        $showcarrier=false;
                        foreach ($value as $key1 => $value1) {
                            
                            // print_r($key1);
                            
                            if ($key1 == 'amount') {
                                $amount = $value1;
                            }
                            
                            if ($key1 == 'service') {
                                $service = $value1;
                            }
                            
                            if ($key1 == 'serviceId') {
                                $serviceId = $value1;
                            }

                             if ($key1 == 'carrier') {
                                $carrier = $value1;
                            }

                             if ($key1 == 'carrierId') {
                                $carrierId = $value1;
                            }

                             if ($showcarrier == 'showcarrier') {
                                $showcarrier = $value1;
                            }
                       
                       
                     

 
 }
        
        $service=$carrier.' '.$service;  
        $carrier='';               
 
      


  // print_r($service.'<--->'.$serviceId.'<--->'.$amount.'<br />');
 // print_r($this->_getDefaultRate());
 $rate = Mage::getModel('shipping/rate_result_method');
 
    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle($carrier);
    $rate->setMethod($serviceId);
    $rate->setMethodTitle($service);
    $rate->setPrice($amount);
    $rate->setCost($amount);
     
                           
 $result->append($rate);


Mage::log('rate to check out==> ');

                    }
 
 

} catch (Exception $e) {

}


// print_r($result);
    return $result;
  }
 
  public function getAllowedMethods()
  {
    return array(
      'soluship_shipping1' => $this->getConfigData('name'),
    );
  }
 
  protected function _getDefaultRate()
  {
    $rate = Mage::getModel('shipping/rate_result_method');
 
    $rate->setCarrier($this->_code);
    $rate->setCarrierTitle('Puralator');
    $rate->setMethod('Fright');
    $rate->setMethodTitle('Groud');
    $rate->setPrice(20);
    $rate->setCost(0);
     
    return $rate;
  }

 
   
}