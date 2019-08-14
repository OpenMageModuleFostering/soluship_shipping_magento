<?php
class Soluship_Shipping1_Model_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{  

     public function __construct()
    {

       // print_r("sachin");

           
         //echo  $this->getRequest()->getRequestUri();
        parent::__construct();
        $this->setId('sales_order_grid');
        $this->setUseAjax(true);
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }
    protected function _prepareMassaction()
    {
        parent::_prepareMassaction();
         

            $carriers=Mage::getStoreConfig('carriers');
$url=$carriers['soluship_shipping1']['shippingserver']."/ecomPrintLabel.action?SolushipAccessKey=".$carriers['soluship_shipping1']['solushipaccesstoekn']."&ecomplatform=magento";
/*$url=$carriers['soluship_shipping1']['shippingserver']."/ecomPrintLabel.action?ecomplatform=magento";*/

 $orderIds = (array)$this->getRequest()->getParam('order_ids'); 
      //     print_r($this); 
        // Append new mass action option
        $this->getMassactionBlock()->addItem(
            'newmodule',
            array('label' => $this->__('Soluship Print Label'),
                  'url'   => $url
            )
        );
    }
}