<?php

/**
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to support@meanbee.com so we can send you a copy immediately.
 *
 * @category   Meanbee
 * @package    Meanbee_Royalmail
 * @copyright  Copyright (c) 2008 Meanbee Internet Solutions (http://www.meanbee.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Soluship_Shipping1_Model_ListAttributes
{

    /**
     * Sets the option array for the weight unit selection
     * in the admin section of the extension
     *
     * @return array
     */
    public function toOptionArray()
    {
       /* $units = array(
            array('value' => 'nil', 'label' => 'N/A'),
            array('value' => 'weightfs', 'label' => 'By Minmum Package Weight'),
            array('value' => 'flatfs', 'label' => 'By Minmum Order Amout')
        );

        return $units;*/
        $attributes = Mage::getResourceModel('catalog/product_attribute_collection')
    ->getItems();


   // return $attributes;
    $units = array();
      array_push($units, array('value' => 'nil', 'label' => 'N/A'));
   /* array('value' => 'nil', 'label' => 'N/A'),
            array('value' => 'weightfs', 'label' => 'By Minmum Package Weight'),
            array('value' => 'flatfs', 'label' => 'By Minmum Order Amout')
*/
 
    foreach ($attributes as $attribute){
        if(trim($attribute->getFrontendLabel())!=''){
        array_push($units,  array('value' => $attribute->getAttributecode(), 'label' => $attribute->getFrontendLabel()));
        }
 } 

    

        return $units;
    }
}
