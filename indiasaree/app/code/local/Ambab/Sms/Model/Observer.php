<?php
 
class Ambab_Sms_Model_Observer {
 
public function newCustomer(Varien_Event_Observer $observer) {

if (Mage::helper('ambab_sms')->notifyNewCustomers()) {
           $event = $observer->getEvent();
           $customer = $event->getCustomer();
           $phone = $customer->getMobileNumber();
           // echo "<pre>";
           // print_r($phone);
           // echo "</pre>";
           // exit;
           $otp = strtoupper(bin2hex(openssl_random_pseudo_bytes(3)));
           //$email = $customer->getEmail();
             
           // $phone = self::getCustomerPhone($customer);
           $templateVariables = array(
               // 'id'    => $customer->getId(),
               // 'name'  => $customer->getName(),
                // 'firstname' => $customer->getFirstname(),
               // 'email'     => $email,
               // 'phone'     => $phone,
               'otp'   => $otp,
           );
           $template = Mage::helper('ambab_sms')->getNewCustomerTemplate();
           $content = self::renderTemplate($template, $templateVariables);
           //self::sendSMS($phone, $content);
       }
}

    // public function newCustomer(Varien_Event_Observer $observer) {
    //      // Mage::log('customer_register_success '.'Mobile==',null,'SendSmsRegister001.log' );  //No log created 
    //      //      $msisdn = $observer->getCustomerAddress()->getTelephone();
    //      //       Mage::log('customer_register_success '.'Mobile=='.$msisdn,null,'SendSmsRegister00.log' );

    //     if (Mage::helper('ambab_sms')->notifyNewCustomers())
    //     {
    //         $event = $observer->getEvent();
    //         $customer = $event->getCustomer(); 
    //         // echo "<pre>";
    //         // print_r($customer);
    //         // echo "</pre>";
    //         $phone = $customer->getMobileNumber();  
    //         $email = $customer->getEmail();
    //         $phone = self::getCustomerPhone($customer);
    //         $templateVariables = array(
    //             'id'    => $customer->getId(),
    //             'name'  => $customer->getName(),
    //             'firstname' => $customer->getFirstname(),
    //             'email'     => $email,
    //             'phone'     => $phone,
    //         );
    //         $template = Mage::helper('ambab_sms')->getNewCustomerTemplate();
    //         $content = self::renderTemplate($template,);
    //         self::sendSMS($phone,$content);
        
    //     }
        
    // }
 
    public function getCustomerPhone($customer) {
            $addresses = array();
            foreach ($customer->getAddresses() as $address) {
                $addresses = $address->toArray();
            }
            $phone = $addresses['telephone'];
 
            return $phone;
    }
 
    public function newOrder(Varien_Event_Observer $observer) {
        if (Mage::helper('ambab_sms')->notifyNewOrders()) {
            $event = $observer->getEvent();
            //$order = $event->getOrder();
           // $customerId = $order->getCustomerId();
            $customer = Mage::getSingleton('customer/session')->getCustomer();
            $phone = self::getCustomerPhone($customer);
            // $templateVariables = array(
            //     'orderid'   => $order->getId(),
            //     'phone'     => $phone,
            //     'name'      => $customer->getName(),
            //     'firstname' => $customer->getFirstname(),
            //     'email'     => $customer->getEmail(),
            // );
            // $template = Mage::helper('ambab_sms')->getNewOrderTemplate();
            // $content = self::renderTemplate($template, $templateVariables);
            self::sendSMS($phone);
        }
    }
     public function newInvoice(Varien_Event_Observer $observer) {
        //if (Mage::helper('ambab_sms')->notifyNewInvoice()) {
            $event = $observer->getEvent();
              $invoice = $event->getInvoice();
       $customerId = $invoice->getCustomerId();
       $phone = Mage::getModel('customer/customer')->load($customerId)->getMobileNumber();

 // echo "<pre>";
 //       echo $customerId;
 //       echo "</pre>";
 //       exit;
 //            //$order = $event->getOrder();
           // $customerId = $order->getCustomerId();
            // $customer = Mage::getSingleton('customer/session')->getCustomer();
            // $phone = self::getCustomerPhone($customer);
            $templateVariables = array();
            $template = Mage::helper('ambab_sms')->getNewOrderTemplate();
            $content = self::renderTemplate($template, $templateVariables);
            self::sendSMS($phone,$content);
       //}
    }
 
    public function newShipment(Varien_Event_Observer $observer) {
        if (Mage::helper('ambab_sms')->notifyShipments()) {
            $event = $observer->getEvent();
            $shipment = $event->getShipment();
            $shipmentId = $shipment->getId();
            $order = $shipment->getOrder();
            $customerId = $order->getCustomerId();
            $customer = Mage::getModel('customer/customer')->load($customerId);
            $phone = self::getCustomerPhone($customer);
            $templateVariables = array(
                'shipmentid'=> $shipmentId,
                'orderid'   => $order->getId(),
                'phone'     => $phone,
                'name'      => $customer->getName(),
                'firstname' => $customer->getFirstname(),
                'email'     => $customer->getEmail(),
            );
            $template = Mage::helper('ambab_sms')->getNewShipmentTemplate();
            $content = self::renderTemplate($template, $templateVariables);
            self::sendSMS($phone, $content);
        }
    }
 
    // public function sendSMS($number) {
    //     if (Mage::helper('ambab_sms')->inProductionMode() && is_numeric($number)) {
    //         $baseUrl = Mage::helper('ambab_sms')->getBaseUrl();
    //         $apiParams = array(
    //             'apikey'    => '64200da0029b2it409pl98r58wotqy6v',
    //             'to'  => $number,
    //             'message'       => 'thanks to register with us',
    //             'sender' => 'SEDEMO'
    //         );
    //         $url = $baseUrl . '?' . http_build_query($apiParams);
    //         $response = file_get_contents($url);
 
    //         if (strpos($response, 'error') !== false){
    //             Mage::log('smspro error @ url : ' . $url . ' response: ' . $response);
    //         }
    //     }
    // }


     public function sendSMS($number,$content) {
        if (Mage::helper('ambab_sms')->inProductionMode() && is_numeric($number)) {
            $baseUrl = Mage::helper('ambab_sms')->getBaseUrl();
            $apiParams = array(
                'apikey'    => '64200da0029b2it409pl98r58wotqy6v',
                'to'  => $number,
                'message'       => $content,
                'sender' => 'SEDEMO'
            );
            $url = $baseUrl . '?' . http_build_query($apiParams);
            // 
            echo $url;
            // exit;
            $response = file_get_contents($url);
 
            if (strpos($response, 'error') !== false){
                Mage::log('smspro error @ url : ' . $url . ' response: ' . $response);
            }
        }
    }
 
 
    /**
     * Renders a given sms template. Replaces all the known
     * variable values from the raw content.
     */
    public function renderTemplate($content, $vars) {
        preg_match_all("/\{[^}]+\}/", $content, $match);
        if (array_key_exists(0, $match)){
            $matches = $match[0];
            foreach($matches as $key => $value) {
                $cleanValue = str_replace('{', '', $value);
                $cleanValue = str_replace('}', '', $cleanValue);
                if (array_key_exists(strtolower($cleanValue), $vars)) {
                    $content = str_replace($value, $vars[$cleanValue], $content);
                }
            }
        }
        return $content;
    }
}
 
?>
