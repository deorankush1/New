<?php
class Shipping_Checkout_Model_Type_Onepage extends Mage_Checkout_Model_Type_Onepage
{
    protected function validateOrder()
    {
        $helper = Mage::helper('checkout');
        if ($this->getQuote()->getIsMultiShipping()) {
            Mage::throwException($helper->__('Invalid checkout type.'));
        }
  
        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            Mage::throwException($helper->__('Please check billing address information.'));
        }
  
        if (!($this->getQuote()->getPayment()->getMethod())) {
            Mage::throwException($helper->__('Please select valid payment method.'));
        }
    }
}