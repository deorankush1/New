<?php
/**
 * RedirectController.php
 * 
 * Copyright (c) 2010-2011 PayU India
 * 
 * 
 * 
 * 
 * @author     Ayush Mittal
 * @copyright  2011-2015 PayU India
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @link       http://www.payu.in
 * @category   PayUbiz
 * @package    PayUbiz
 */

// Include the payubiz common file
define( 'PB_DEBUG', ( Mage::getStoreConfig( 'payment/payubiz/debugging' ) ? true : false ) );
include_once( dirname( __FILE__ ) .'/../payubiz_common.inc' );
 
/**
 * PayUbiz_RedirectController
 */
class PayUbiz_PayUbiz_RedirectController extends Mage_Core_Controller_Front_Action
{
    protected $_order;
	protected $_WHAT_STATUS = false;

    public function getOrder()
    {
       
        return( $this->_order );
    }

    protected function _expireAjax()
    {
     
        if( !Mage::getSingleton( 'checkout/session' )->getQuote()->hasItems() )
        {
            $this->getResponse()->setHeader( 'HTTP/1.1', '403 Session Expired' );
            exit;
        }
    }
    
    protected function _getCheckout()
    {
      
        return Mage::getSingleton( 'checkout/session' );
    }
  
	public function getQuote()
    {
      
        return $this->getCheckout()->getQuote();
    }
 
    public function getStandard()
    {
        
        return Mage::getSingleton( 'payubiz/shared' );
    }
    
	public function getConfig()
    {
       
        return $this->getStandard()->getConfig();
    }

    protected function _getPendingPaymentStatus()
    {
         
        return Mage::helper( 'payubiz' )->getPendingPaymentStatus();
    }
  
    public function redirectAction()
    {

        pblog( 'Redirecting to payubiz' );
        
		try
        {
            $session = Mage::getSingleton( 'checkout/session' );

            $order = Mage::getModel( 'sales/order' );
            $order->loadByIncrementId( $session->getLastRealOrderId() );
        
            if( !$order->getId() )
                Mage::throwException( 'No order for processing found' );
        
            if( $order->getState() != Mage_Sales_Model_Order::STATE_PENDING_PAYMENT )
            {
                $order->setState(
                    Mage_Sales_Model_Order::STATE_PENDING_PAYMENT,
                    $this->_getPendingPaymentStatus(),
                    Mage::helper( 'payubiz' )->__( 'Customer was redirected to payubiz.' )
                )->save();
            }

            if( $session->getQuoteId() && $session->getLastSuccessQuoteId() )
            {
                $session->setpayubizQuoteId( $session->getQuoteId() );
                $session->setpayubizSuccessQuoteId( $session->getLastSuccessQuoteId() );
                $session->setpayubizRealOrderId( $session->getLastRealOrderId() );
                $session->getQuote()->setIsActive( false )->save();
                $session->clear();
            }

			$r = $this->getResponse()->setBody( $this->getLayout()->createBlock( 'payubiz/request' )->toHtml() );  

        
	        $session->unsQuoteId();
            
            return;
        }
        catch( Mage_Core_Exception $e )
        {
            $this->_getCheckout()->addError( $e->getMessage() );
        }
        catch( Exception $e )
        {
            Mage::logException($e);
        }       
        
        $this->_redirect( 'checkout/cart' );
    }
   
    public function cancelAction()
    {
        
		// Get the user session
        Mage::getSingleton('core/session')->unsMaxGetMoreAmount();
        $session = Mage::getSingleton( 'checkout/session' );
        $session->setQuoteId( $session->getpayubizQuoteId( true ) );
		$session = $this->_getCheckout();


        $arrParams = $this->getRequest()->getParams();
        // Mage::getModel('payubiz/shared')->getResponseOperation($arrParams);
       
        
        if( $quoteId = $session->getpayubizQuoteId() )
        {
            $quote = Mage::getModel( 'sales/quote' )->load( $quoteId );
            
            if( $quote->getId() )
            {
                $quote->setIsActive( true )->save();
                $session->setQuoteId( $quoteId );
            }
        }
		
        // Cancel order
		$order = Mage::getModel( 'sales/order' )->loadByIncrementId( $session->getLastRealOrderId() );
		if( $order->getId() ){
            $order->cancel();
            $order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true, 'Error occured while processing your transaction');
            $order->setStatus('payment_canceled'); 
            $order->save();
        }
        
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(1)->save();
        Mage::getSingleton('checkout/session')->getQuote()->setReservedOrderId(null)->save();
        
        $this->_redirect('checkout/onepage/failure');
    }

    public function cancelOrderItems($order){
        Mage::getSingleton('core/session')->unsMaxGetMoreAmount();
        $resource = Mage::getSingleton('core/resource');
        $writeConnection = $resource->getConnection('core_write');

        // foreach ($orderModel as $key => $order) {
            foreach($order->getAllItems() as $item){
                $qty = $item->getQtyOrdered();
                $itemId = $item->getItemId();
                $taxAmount = $item->getTaxAmount();
                $HiddenTaxCanceled = 0;
                $IncrementIdArray[] = $order->getIncrementId();

                $query =  "UPDATE sales_flat_order_item SET qty_canceled = '".$qty."', tax_canceled = '".$taxAmount."', hidden_tax_canceled = '".$HiddenTaxCanceled."', item_state = 'canceled', item_status = 'canceled' WHERE order_id = '".$order->getEntityId()."' AND item_id = '".$itemId."'";

                $writeConnection->query($query);
                
                // $product = Mage::getModel('catalog/product')->load($item->getProductId());
                // $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($product->getId());
                // $qtyStock = $stockItem->getQty();
                // $updated_inventory=$qtyStock + $qty;
                // if ($stockItem->getId() > 0 and $stockItem->getManageStock()) {
                //     $stockItem->setQty($updated_inventory);
                //     $stockItem->setIsInStock((int)($updated_inventory > 0));
                //     $stockItem->save();
                //     $product->save();

                //   } 

            }
        // }
    }

     public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }


     public function failureAction(){
       $arrParams = $this->getRequest()->getPost();
       Mage::getModel('payubiz/shared')->getResponseOperation($arrParams);
       $this->getCheckout()->clear();
        
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(1)->save();
        Mage::getSingleton('checkout/session')->getQuote()->setReservedOrderId(null)->save();
       Mage::getSingleton('core/session')->unsMaxGetMoreAmount();
        $this->_redirect('checkout/onepage/failure');
    }


    public function successAction()
    {     
      
		try
        {
			$session = Mage::getSingleton( 'checkout/session' );
			$session->unspayubizRealOrderId();
			$session->setQuoteId( $session->getpayubizQuoteId( true ) );
			$session->setLastSuccessQuoteId( $session->getpayubizSuccessQuoteId( true ) );
            $response = $this->getRequest()->getPost();
            Mage::getModel('payubiz/shared')->getResponseOperation($response);
           
			$this->_redirect( 'checkout/onepage/success', array( '_secure' => true ) );
			
            return;
		}
        catch( Mage_Core_Exception $e )
        {
			$this->_getCheckout()->addError( $e->getMessage() );
		}
        catch( Exception $e )
        {
			Mage::logException( $e );
		}
		
        $this->_redirect('checkout/onepage/success');
    }
   
}