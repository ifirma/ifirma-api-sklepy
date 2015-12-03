<?php
class Powermedia_Ifirma_Model_RyczaltRates
{
   public function toOptionArray()
   {
       $themes = array(
           array('value' => '0.03', 'label' => '3%'),
           array('value' => '0.055','label' => '5,5%'),
           array('value' => '0.085', 'label' => '8,5%'),
           array('value' => '0.17', 'label' => '17%'),
           array('value' => '0.2',  'label' => '20%'),
       );
 
       return $themes;
   }
}
?>
