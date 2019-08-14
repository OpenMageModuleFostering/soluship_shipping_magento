<?php
class Soluship_Shipping1_Model_Sales_Order_View  extends Mage_Adminhtml_Block_Sales_Order_View	
{

	 public function  __construct() {


 	$carriers=Mage::getStoreConfig('carriers');
	 		$url=$carriers['soluship_shipping1']['shippingserver']."/ecomPrintLabel.action?SolushipAccessKey=".$carriers['soluship_shipping1']['solushipaccesstoekn']."&ids=".$this->getOrderId().",&ecomplatform=magento";
	 	/*	$carriers=Mage::getStoreConfig('carriers');
	 		$url=$carriers['soluship_shipping1']['shippingserver']."/ecomPrintLabel.action?ids=".$this->getOrderId().",";*/

 
 
 if($this->getOrder()->getStatusLabel()!="Canceled"){
  
 
/*
 $this->_addButton('testbutton11', array(
            'label'     => Mage::helper('Sales')->__('Soluship Print Label'),
            'onclick'   => 'setLocation(\'' . $url . '\')',
            'class'     => 'go'
        ), 0, 100, 'header', 'header');
*/
 

     
 }

       

        parent::__construct();

    }
}