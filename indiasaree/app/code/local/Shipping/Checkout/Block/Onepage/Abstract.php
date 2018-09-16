<?php
require_once 'Mage/Checkout/Block/Onepage/Abstract.php';
  
class Shipping_Checkout_Block_Onepage_Abstract extends Mage_Checkout_Block_Onepage_Abstract{
 		protected function _getStepCodes()
    	{
      	  return array('login', 'billing', 'payment', 'review');
    	}
	}