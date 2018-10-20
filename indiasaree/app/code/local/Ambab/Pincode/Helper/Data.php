<?php

 class Ambab_Pincode_Helper_Data extends Mage_Core_Helper_Abstract {


 	public function getWishlistCount(){
 		if (Mage::getSingleton('customer/session')->isLoggedIn()){
                $customer = Mage::getSingleton('customer/session')->getCustomer();
                $wishlist = Mage::getModel('wishlist/wishlist')->loadByCustomer($customer, true);
                return $wishlist->getItemsCount();
        }
        else{
        	return "0";
        } 
 	}

}

?>