<?php
/**
 * Standard.php
 * 
 * Copyright (c) 2011-2015 PayU India
 * 
 * 
 * @author     Ayush Mittal 
 * @copyright  2011-2015 PayU India
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://www.payu.in
 * @category   PayUbiz
 * @package    PayUbiz_PayUbiz
 */

/**
 * PayUbiz_PayUbiz_Model_Standard
 */
class PayUbiz_PayUbiz_Model_Debitcard extends Mage_Payment_Model_Method_Abstract
{
    protected $_code = 'payucheckout_debitcard';
    protected $_formBlockType = 'payubiz/form';
    protected $_infoBlockType = 'payubiz/payment_info';
    protected $_order;
    
    protected $_isGateway              = true;
    protected $_canAuthorize           = true;
    protected $_canCapture             = true;
    protected $_canCapturePartial      = true;
    protected $_canRefund              = false;
    protected $_canVoid                = true;
    protected $_canUseInternal         = true;
    protected $_canUseCheckout         = true;
    protected $_canUseForMultishipping = true;
    protected $_canSaveCc              = false;

  
    public function getCheckout()
    {
        
        return Mage::getSingleton( 'checkout/session' );
    }

    public function getQuote()
    {
      
        return $this->getCheckout()->getQuote();
    }
  
    public function getConfig()
    {
       
        return Mage::getSingleton( 'payubiz/config' );
    }
   
    public function getOrderPlaceRedirectUrl()
    {
       
        return Mage::getUrl( 'payubiz/redirect/redirect', array( '_secure' => true ) );
    }
 
    public function getSuccessUrl()
    {
      
        return Mage::getUrl( 'payubiz/redirect/success', array( '_secure' => true ) );
    }
 
    public function getCancelUrl()
    {
        return Mage::getUrl( 'payubiz/redirect/cancel', array( '_secure' => true ) );
    }
  
    public function getfailureUrl()
    { 
      
        return Mage::getUrl( 'payubiz/redirect/failure', array( '_secure' => true ) );
    }
  
    public function getRealOrderId()
    {
      
        return Mage::getSingleton( 'checkout/session' )->getLastRealOrderId();
    }
  
    public function getNumberFormat( $number )
    {

        return number_format( $number, 2, '.', '' );
    }

    public function getTotalAmount( $order )
    {
      
        if( $this->getConfigData( 'use_store_currency' ) )
        {
            // if(!empty(Mage::getSingleton('core/session')->getMaxGetMoreAmount()))
            // {
            //   $totals=$order->getGrandTotal()-Mage::getSingleton('core/session')->getMaxGetMoreAmount();
            //    $price = $this->getNumberFormat( $totals );
            // }
            // else
            // {
            // $price = $this->getNumberFormat( $order->getGrandTotal() );
            // }
           $price = $this->getNumberFormat( $order->getGrandTotal() );
          }
        else
        {
          // if(!empty(Mage::getSingleton('core/session')->getMaxGetMoreAmount()))
          //   {
          //     $totals=$order->getGrandTotal()-Mage::getSingleton('core/session')->getMaxGetMoreAmount();
          //      $price = $this->getNumberFormat( $totals );
          //   }
          //   else
          //   {
          //   $price = $this->getNumberFormat( $order->getBaseGrandTotal() );
          //   }
            
               $price = $this->getNumberFormat( $order->getBaseGrandTotal() );
        }

        return $price;
    }
 
    public function getStoreName()
    {
       
        $store_info = Mage::app()->getStore();
        return $store_info->getName();
    }
  
    public function getStandardCheckoutFormFields()
    {
        // Variable initialization
      // echo "hii";exit;
        // echo $orderIncrementId;exit;
        $orderIncrementId = $this->getCheckout()->getLastRealOrderId();
      // echo $orderIncrementId;exit;
        $order = Mage::getModel( 'sales/order' )->loadByIncrementId( $orderIncrementId );
        $description = '';

       $transaction_mode = $this->getConfigData('trans_mode');
       $merchant_key = $this->getConfigData('merchant_key');   
       $salt = $this->getConfigData('salt');   
       $payment_gateway = $this->getConfigData('Pg');   
       $bankcode = $this->getConfigData('bankcode');  

       $txn_random_no = $this->getConfigData('txn_random_no');  

       $currency_convertor = $this->getConfigData('currency_convertor');   

      

        if($transaction_mode == 'test')
        {
            $txnid = $orderIncrementId.$txn_random_no;
        } else
        {
           $txnid = $orderIncrementId;
        } 
        // Create description
        foreach( $order->getAllItems() as $items )
        {
            $totalPrice = $this->getNumberFormat( $items->getQtyOrdered() * $items->getPrice() );

            $description .=
                $this->getNumberFormat( $items->getQtyOrdered() ) .
                ' x '. $items->getName() .
                ' @ '. $order->getOrderCurrencyCode() . $this->getNumberFormat( $items->getPrice() ) .
                ' = '. $order->getOrderCurrencyCode() . $totalPrice .'; ';
        }
        $description .= 'Shipping = '. $order->getOrderCurrencyCode() . $this->getNumberFormat( $order->getShippingAmount() ).';';
        $description .= 'Total = '. $order->getOrderCurrencyCode() . $this->getTotalAmount( $order ).';'; 

        /**********************************************************************************/ 

        

        //$order = Mage::getModel('sales/order')->load (MY_ORDER_ID); 
        //If they have no customer id, they're a guest. 
        // if($order->getCustomerId() === NULL)
        // { 
          $billingaddress =  $order->getBillingAddress(); 
        // } //else, they're a normal registered user. 
        // else { 
        //   $customer = Mage::getModel('customer/customer')->load($order->getCustomerId()); 
        //  $billingaddress =  $customer->getDefaultBillingAddress()->getFirstname(); 
        // } 


        /**********************************************************************************/ 

       

        //$billingaddress = $customer->getPrimaryBillingAddress()->getData();    

        $countryName = Mage::getModel('directory/country')->load($billingaddress->getCountryId())->getName();   

       
        if($currency_convertor != 0 && $order->getData('base_currency_code')!='INR')
        {
        
        $getAmount = file_get_contents("http://www.google.com/finance/converter?a=".$this->getTotalAmount( $order )."&from=".$order->getData('base_currency_code')."&to=INR"); 

        $getAmount = explode("<span class=bld>",$getAmount);
        $getAmount = explode("</span>",$getAmount[1]);
        $convertedAmount = preg_replace("/[^0-9\.]/", null, $getAmount[0]);
        $calculatedAmount_INR = round($convertedAmount,2);       

       } else {

         $calculatedAmount_INR = $this->getTotalAmount( $order ); 

       }
        
        // Construct data for the form
        $data = array(
            // Merchant details
            'key' => $merchant_key,
            'txnid' => $txnid,
            'amount' => $calculatedAmount_INR,
            'productinfo' => 'Product Information',

             // Buyer details
            'firstname' => $order->getData('customer_firstname'),          
            'Lastname' =>  $order->getData('customer_lastname'),
            'City' => $billingaddress->getCity(),
            'State' => $billingaddress->getRegion(),             
            'Country' => $countryName,
            'Zipcode' => $billingaddress->getPostcode(),          
            'email' => $order->getData('customer_email'),            
            'phone' => $billingaddress->getTelephone(),
            'surl' => $this->getSuccessUrl(),
            'furl' => $this->getfailureUrl(),
'curl' => $this->getCancelUrl(),
      'offer_key' =>'DEBIT@7265'      
        );
            $data['pg'] = $payment_gateway;
            $data['bankcode'] = $bankcode;

           $data['Hash']         =   strtolower(hash('sha512', $merchant_key.'|'.$txnid.'|'.$data['amount'].'|'.
 $data['productinfo'].'|'.$data['firstname'].'|'.$data['email'].'|||||||||||'.$salt));   

      if(PB_DEBUG) {

        Mage::log('payubiz'.Zend_Debug::dump($data, null, false), null, 'payubiz.log');

        }
        return( $data );
    }



    public function initialize( $paymentAction, $stateObject )
    {
      
        $state = Mage_Sales_Model_Order::STATE_PENDING_PAYMENT;
        $stateObject->setState( $state );
        $stateObject->setStatus( 'pending_payment' );
        $stateObject->setIsNotified( false );
    }

    public function getResponseOperation($response)
    {       

           $order = Mage::getModel('sales/order');  
    
        $transaction_mode = $this->getConfigData('trans_mode');
        $merchant_key = $this->getConfigData('merchant_key');   
         $salt = $this->getConfigData('salt');  

         $txn_random_no = $this->getConfigData('txn_random_no');  

        if(isset($response['status']))
        {
           $txnid=$response['txnid'];

           $transaction_mode = $this->getConfigData('trans_mode');

            if($transaction_mode == 'test')
            {
               
                $orderid=$txnid.$txn_random_no;
            } else
            {
              $orderid=$txnid;
            } 
          
           
           if($response['status']=='success' || $response['status']=='in progress')
            {
                $status=$response['status'];
                $order->loadByIncrementId($orderid);
                $billing = $order->getBillingAddress();
                $amount      = $response['amount'];
                $productinfo = $response['productinfo'];  
                $firstname   = $response['firstname'];
                $email       = $response['email'];
                $keyString='';
                $Udf1 = $response['udf1'];
                $Udf2 = $response['udf2'];
                $Udf3 = $response['udf3'];
                $Udf4 = $response['udf4'];
                $Udf5 = $response['udf5'];
                $Udf6 = $response['udf6'];
                $Udf7 = $response['udf7'];
                $Udf8 = $response['udf8'];
                $Udf9 = $response['udf9'];
                $Udf10 = $response['udf10'];
               
                $keyString =  $merchant_key.'|'.$txnid.'|'.$amount.'|'.$productinfo.'|'.$firstname.'|'.$email.'|'.$Udf1.'|'.$Udf2.'|'.$Udf3.'|'.$Udf4.'|'.$Udf5.'|'.$Udf6.'|'.$Udf7.'|'.$Udf8.'|'.$Udf9.'|'.$Udf10;    
             
                
                $keyArray = explode("|",$keyString);
                $reverseKeyArray = array_reverse($keyArray);
                $reverseKeyString=implode("|",$reverseKeyArray);
                $saltString     = $salt.'|'.$status.'|'.$reverseKeyString;
                $sentHashString = strtolower(hash('sha512', $saltString));
                 $responseHashString=$_REQUEST['hash'];
                
                if($sentHashString==$responseHashString && $order->getStatus() != 'payment_processing')
                {
                  // if ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED) {
                  //   $this->uncancelOrder($order);
                  // }
                  $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
                  $order->setStatus('payment_processing');
                  $history = $order->addStatusHistoryComment("<b>Payu Txn Status: </b>".$response['status']."<br>"."<b>Payu Txn ID: </b>".$txnid, false);
                      $history->setIsCustomerNotified(false);
                  foreach($order->getAllItems() as $item){

                    $query =  "UPDATE sales_flat_order_item SET item_state = 'processing', item_status = 'processing' WHERE order_id = '".$order->getEntityId()."' AND item_id = '".$itemId."'";
                    $writeConnection->query($query);
            
                  }
                  $order->save();
                  $order->sendNewOrderEmail();
                }
                else
                {
                  $order->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
                  $order->setStatus('payment_pending');
                  $order->save();
                }
                
                if ($debug_mode==1) {
                    $debugId=$response['udf1'];  
                    $data = array('response_body'=>implode(",",$response));
                    $model = Mage::getModel('payucheckout/api_debug')->load($debugId)->addData($data);
                    $model->setId($id)->save();
                  }
               }
           
           if($response['status']=='failure')
           {
              
                $order->loadByIncrementId($orderid);
                $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
                $order->setStatus('payment_canceled');
                $history = $order->addStatusHistoryComment("<b>Payu Txn Status: </b>".$response['status']." (Canceled by user.) "."<br>"."<b>Payu Txn ID: </b>".$txnid, false);

                $history->setIsCustomerNotified(false);
               // Inventory updated 
               // $this->updateInventory($orderid);
               
               $order->cancel()->save();
               
               if ($debug_mode==1) {
                $debugId=$response['udf1'];
                        $data = array('response_body'=>implode(",",$response));
                    $model = Mage::getModel('payucheckout/api_debug')->load($debugId)->addData($data);
                    $model->setId($id)->save();
                  }
           
           }
           else  if($response['status']=='pending')
           {
                
               $order->loadByIncrementId($orderid);
               $order->setState(Mage_Sales_Model_Order::STATE_NEW, true);
               $order->setStatus('payment_pending');
               // Inventory updated  
               // $this->updateInventory($orderid);
               $order->save();
                       
               if ($debug_mode==1) {
                $debugId=$response['udf1'];
                        $data = array('response_body'=>implode(",",$response));
                    $model = Mage::getModel('payucheckout/api_debug')->load($debugId)->addData($data);
                    $model->setId($id)->save();
                  }
           
           }
           
        }
        else
        {
                   
           $order->loadByIncrementId($response['id']);
           $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true);
            $order->setStatus('payment_canceled');
            $history = $order->addStatusHistoryComment("<b>Payu Txn Status: </b>".$response['status']."<br>"."<b>Payu Txn ID: </b>".$txnid, false);
            $history->setIsCustomerNotified(false);
          // Inventory updated 
           $order_id=$response['id'];
           // $this->updateInventory($order_id);
           
           $order->cancel()->save();
           
         
        }       

    }

   

    public function updateInventory($order_id)
    {
  
        $order = Mage::getModel('sales/order')->loadByIncrementId($order_id);
        $items = $order->getAllItems();
        foreach ($items as $itemId => $item)
        {
           $ordered_quantity = $item->getQtyToInvoice();
           $sku=$item->getSku();
           $product = Mage::getModel('catalog/product')->load($item->getProductId());
           $qtyStock = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId())->getQty();
          
           $updated_inventory=$qtyStock + $ordered_quantity;
                    
           $stockData = $product->getStockItem();
           $stockData->setData('qty',$updated_inventory);
           $stockData->save(); 
            
       } 
    }
  
    public function getPayUbizUrl()
    {
       
        switch( $this->getConfigData( 'trans_mode' ) )
        {
            case 'test':
                $url = 'https://test.payu.in/_payment';
                break;
            case 'live':
            default :
                $url = 'https://secure.payu.in/_payment';
                break;
        }
        
        return( $url );
    }

    public function uncancelOrder($order){
      $order->setState(Mage_Sales_Model_Order::STATE_PROCESSING);
      $order->setStatus('payment_confirmed');
      $history = $order->addStatusHistoryComment("Order uncanceled by automation tool.", false);
      $history->setIsCustomerNotified(false);

      $order->setBaseDiscountCanceled(0);
      $order->setBaseShippingCanceled(0);
      $order->setBaseSubtotalCanceled(0);
      $order->setBaseTaxCanceled(0);
      $order->setBaseTotalCanceled(0);
      $order->setDiscountCanceled(0);
      $order->setShippingCanceled(0);
      $order->setSubtotalCanceled(0);
      $order->setTaxCanceled(0);
      $order->setTotalCanceled(0);

      foreach($order->getAllItems() as $item){
          $item->setQtyCanceled(0);
          $item->setTaxCanceled(0);
          $item->setHiddenTaxCanceled(0);
          $item->setItemStatus('payment_pending');
          $item->setItemState('new');
          $item->save();
      }

      $order->save();
    }
    
}
