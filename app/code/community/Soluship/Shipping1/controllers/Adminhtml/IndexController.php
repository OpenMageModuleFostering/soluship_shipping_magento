  <?php

class Soluship_Shipping1_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action
{
    protected function _initOrder()
    {
        $id    = $this->getRequest()->getParam('order_id');
        $order = Mage::getModel('sales/order')->load($id);
        
        if (!$order->getId()) {
            $this->_getSession()->addError($this->__('This order no longer exists.'));
            $this->_redirect('*/*/');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }
        Mage::register('sales_order', $order);
        Mage::register('current_order', $order);
        return $order;
    }
    
    
    /*public function rtoAction()
    {
        if ($order = $this->_initOrder()) {
            try {
                $order->setState('rto', true)->save();
                $this->_getSession()->addSuccess($this->__('The order state has been changed.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order state has not been changed.'));
                Mage::logException($e);
            }
            $this->_redirect('adminhtml/sales_order/view', array(
                'order_id' => $order->getId()
            ));
        }
        
    }*/
    
    public function shipmentAction()
    {

$carriers = Mage::getStoreConfig('carriers');
                

        if ($order = $this->_initOrder()) {
            try {
                            //$order->setState('shipment', true)->save();
                  $url1=$carriers['soluship_shipping1']['shippingserver']."/ecomOrderRepeat.action?SolushipAccessKey=".$carriers['soluship_shipping1']['solushipaccesstoekn']."&ids=".$order->entity_id.",&ecomplatform=magento&recreate=true";

                
                $converter = Mage::getModel('sales/convert_order');
                $shipment  = $converter->toShipment($order);
                
                
                $customer = Mage::getModel('customer/customer')->load($order->getData('customer_id'));
                
                $obj = new Soluship_Shipping1_Adminhtml_IndexController();
                
                $billingaddress1 = $order->getBillingAddress();
                
                $billingaddress = array(
                    'customerName' => $order->getData('firstname'),
                    'lastName' => $order->getData('lastname'),
                    'companyName' => $billingaddress1->getData('company'),
                    'telephone' => $billingaddress1->getData('telephone'),
                    'email' => $billingaddress1->getData('email'),
                    'street' => $billingaddress1->getData('street'),
                    'city' => $billingaddress1->getData('city'),
                    'region' => $billingaddress1->getData('region'),
                    'postcode' => $billingaddress1->getData('postcode'),
                    'country' => $billingaddress1->getData('country')
                );
                
                $shipaddress = $shipment->getShippingAddress();
                
                
                
                $ShippingAddress = array(
                    'companyName' => $shipaddress->getData('company'),
                    'customerName' => $shipaddress->getData('firstname'),
                    'lastName' => $shipaddress->getData('lastname'),
                    'telephone' => $shipaddress->getData('telephone'),
                    'email' => $shipaddress->getData('email'),
                    'street' => $shipaddress->getData('street'),
                    'city' => $shipaddress->getData('city'),
                    'region' => $shipaddress->getData('region'),
                    'postcode' => $shipaddress->getData('postcode'),
                    'country' => $shipaddress->getCountry()
                );
                
                $shipmethod = $order->getShippingMethod();
                
                
                $ShippingMethod = array(
                    'shipping' => $order->getData('shipping_method')
                    
                );
                
                
                  $obj->recreate          =true;
                $obj->biladd          = $billingaddress;
                $obj->grandTotal      = $order->getGrandTotal();
                $obj->shipping_method = $ShippingMethod;
                
                
                $obj->ShippingAddress = $ShippingAddress;
                $obj->id              = $order->getId();
                $obj->shippingPackage = $this->createOrderItems($order);
                
                
                $obj->ship_setting = Mage::getStoreConfig('shipping');
                
                $carriers              = Mage::getStoreConfig('carriers');
                $obj->soluship_setting = $carriers;
                
                $store_info         = Mage::getStoreConfig('general');
                $obj->magento_store = $store_info;
                
                $obj->currency    = Mage::getStoreConfig('currency');
                $obj->store_email = Mage::getStoreConfig('trans_email');
                
                
                
                
                $client = new Varien_Http_Client($carriers['soluship_shipping1']['shippingserver'] . '/api/v1/createShipment');
                $client->setMethod(Varien_Http_Client::POST);
                $client->setHeaders('x-magento-shop-domain: MAGENTO');
                /*$client->setHeaders('username: '.$carriers['soluship_shipping1']['apiusername']);
                $client->setHeaders('password: '.$carriers['soluship_shipping1']['apipassword']);*/
                $client->setHeaders('SolushipAccessKey: ' . $carriers['soluship_shipping1']['solushipaccesstoekn']);
                $client->setHeaders('SolushipHost: ' . $_SERVER['SERVER_NAME']);
                
                $json = json_encode($obj);
                
                $client->setRawData($json, 'application/json');
                $response = $client->request();
                
                
                
                
                
                $this->_getSession()->addSuccess($this->__('The order will created in soluship.'));
            }
            
            catch (Exception $e) {
                $this->_getSession()->addError($this->__('The order is not created in soluship.'));
                Mage::logException($e);
            }

 
 
             $this->_redirectUrl($url1);
            
            
        }
        
        
        
        
        
        
        
    }
    
    
    
    
    public function createOrderItems($order)
    {
        
        $carriers = Mage::getStoreConfig('carriers');
        $lenghtc  = $carriers['soluship_shipping1']['length_attribute'];
        $heightc  = $carriers['soluship_shipping1']['height_attribute'];
        $widthc   = $carriers['soluship_shipping1']['width_attribute'];
        
        $items     = $order->getAllVisibleItems();
        $i         = 0;
        $IO        = array();
        $itemcount = count($items);
        $ww        = 0.0;
        
        $storeId = Mage::app()->getStore()->getStoreId();
        
        
        
        foreach ($items as $item) {
            $IO[$i]['name']  = $item->getName();
            $IO[$i]['price'] = $item->getPrice();
            $IO[$i]['sku']   = $item->getSku();
            $IO[$i]['id']    = $item->getProductId();
            
            
            $cProduct = Mage::getModel('catalog/product')->load($item->getProductId());
            Mage::log($cProduct[$lenghtc]);
            $IO[$i]['length'] = $cProduct[$lenghtc];
            $IO[$i]['height'] = $cProduct[$heightc];
            $IO[$i]['width']  = $cProduct[$widthc];
            
            //$cProduct = Mage::getModel('catalog/product');
            
            $cProduct = Mage::getModel('catalog/product')->load($item->getProductId());
            //$cProduct->load($item->getProductId());
            $json1    = Mage::helper('core')->jsonEncode($cProduct);
            
            $pweight = $cProduct->getWeight();
            
            
            $IO[$i]['qty']    = $item->getQtyOrdered();
            $IO[$i]['weight'] = $pweight;
            $qty              = $item->getQtyOrdered();
            $ww               = $ww + ((double) $pweight * (int) $qty);
            
            if ($itemcount > 1) {
                $IO[$i]['shipping'] = $order->getData('shipping_amount') / $itemcount;
                $IO[$i]['tax']      = $order->getData('tax_amount') / $itemcount;
            } elseif ($itemcount == 1) {
                $IO[$i]['shipping'] = $order->getData('shipping_amount');
                $IO[$i]['tax']      = $order->getData('tax_amount');
            }
            
            
            $i++;
        }
        
        
        
        
        
        $packs = array(
            'items' => $IO,
            'package_weight' => $ww
            
        );
        
        
        
        
        return $packs;
    }
    
    
    
}