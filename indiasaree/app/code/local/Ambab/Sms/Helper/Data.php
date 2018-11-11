<?php
 
class Ambab_Sms_Helper_Data extends Mage_Core_Helper_Abstract {

	const OPT_BASE_URL = 'smspro/general/baseurl';
    const OPT_PRODUCTION = 'smspro/general/active';
    const OPT_USERNAME = 'smspro/general/username';
    const OPT_PASSWORD = 'smspro/general/password';
    const OPT_OVERRIDE_DND = 'smspro/general/overridednd';
 
    const OPT_NOTI_NEW_CUSTOMER = 'smspro/smsevents/new_customer';
    const OPT_NOTI_NEW_ORDER = 'smspro/smsevents/new_order';
    const OPT_NOTI_SHIPMENT = 'smspro/smsevents/shipment';
    const OPT_NOTI_NEW_INVOICE = 'smspro/smsevents/new_invoice';
 
    const OPT_TEMP_NEW_CUSTOMER = 'smspro/templates/new_customer_template';
    const OPT_TEMP_NEW_ORDER = 'smspro/templates/new_order_template';
    const OPT_TEMP_NEW_SHIPMENT = 'smspro/templates/shipment_template';
    const OPT_TEMP_NEW_INVOICE = 'smspro/templates/invoice_template';
  
 
    public function getBaseUrl($store = null) {
        return Mage::getStoreConfig(self::OPT_BASE_URL, $store);
    }
 
    public function inProductionMode($store = null) {
        return (bool) Mage::getStoreConfig(self::OPT_PRODUCTION, $store);
    }
 
    public function getGatewayUsername($store = null) {
        return Mage::getStoreConfig(self::OPT_USERNAME, $store);
    }
 
    public function getGatewayPassword($store = null) {
        return Mage::getStoreConfig(self::OPT_PASSWORD, $store);
    }
 
    public function notifyNewCustomers($store = null) {
        return (bool) Mage::getStoreConfig(self::OPT_NOTI_NEW_CUSTOMER, $store);
    }
 
    public function notifyNewOrders($store = null) {
        return (bool) Mage::getStoreConfig(self::OPT_NOTI_NEW_ORDER, $store);
    }

    public function notifyNewInvoice($store = null) {
        return (bool) Mage::getStoreConfig(self::OPT_NOTI_NEW_INVOICE, $store);
    }
 
    public function notifyShipments($store = null) {
        return (bool) Mage::getStoreConfig(self::OPT_NOTI_SHIPMENT, $store);
    }
 
    public function overrideDnd($store = null) {
        return (bool) Mage::getStoreConfig(self::OPT_OVERRIDE_DND, $store);
    }
 
    public function getNewCustomerTemplate($store = null) {
        return Mage::getStoreConfig(self::OPT_TEMP_NEW_CUSTOMER, $store);
    }
 
    public function getNewOrderTemplate($store = null) {
        return Mage::getStoreConfig(self::OPT_TEMP_NEW_ORDER, $store);
    }
 
    public function getNewShipmentTemplate($store = null) {
        return Mage::getStoreConfig(self::OPT_TEMP_NEW_SHIPMENT, $store);
    }


     public function getNewInvoiceTemplate($store = null) {
        return Mage::getStoreConfig(self::OPT_TEMP_NEW_INVOICE, $store);
    }
}

?>
