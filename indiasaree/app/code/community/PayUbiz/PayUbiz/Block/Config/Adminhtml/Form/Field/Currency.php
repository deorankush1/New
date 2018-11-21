<?php
class PayUbiz_PayUbiz_Block_Config_Adminhtml_Form_Field_Currency
    extends Mage_Core_Block_Html_Select
{
    public function _toHtml(){
        
        $allowedCurrencies = explode(',', Mage::getStoreConfig('currency/options/allow', Mage::app()->getStore()->getId()));
        
        foreach ($allowedCurrencies as $value) {
            $this->addOption($value,$value);
        }


        return parent::_toHtml();
    }

    public function setInputName($value)
    {
        return $this->setName($value);
    }
}