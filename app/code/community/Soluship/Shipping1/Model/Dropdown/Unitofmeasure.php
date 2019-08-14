<?php
 
class Soluship_Shipping1_Model_dropdown_unitofmeasure
{
    /**
     * Return array of Measure units
     *
     * @return array
     */
    public function toOptionArray()
    {
        
        $result = array();
        
           /* $result[] = array('value'=>'lb','label'=>'Pound');
            $result[] = array('value'=>'kg','label'=>'Kill Gram');
        */
        return $result;
    }
}
