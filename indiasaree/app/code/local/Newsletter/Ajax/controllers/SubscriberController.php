<?php
//require_once 'Mage/Newsletter/controllers/SubscriberController.php';
class Newsletter_Ajax_SubscriberController extends Mage_Core_Controller_Front_Action {

    /**
     * New subscription action
     */
    public function newajaxAction()
    {
         if ($this->getRequest()->isPost() && $this->getRequest()->getPost('email')) {
            $session            = Mage::getSingleton('core/session');
            $customerSession    = Mage::getSingleton('customer/session');
            $email              = (string) $this->getRequest()->getPost('email');

            try {
                if (!Zend_Validate::is($email, 'EmailAddress')) {
                    Mage::throwException($this->__('Please enter a valid email address.'));
                }

                if (Mage::getStoreConfig(Mage_Newsletter_Model_Subscriber::XML_PATH_ALLOW_GUEST_SUBSCRIBE_FLAG) != 1 && 
                    !$customerSession->isLoggedIn()) {
                    Mage::throwException($this->__('Sorry, but administrator denied subscription for guests. Please <a href="%s">register</a>.', Mage::helper('customer')->getRegisterUrl()));
                }

                $ownerId = Mage::getModel('customer/customer')
                        ->setWebsiteId(Mage::app()->getStore()->getWebsiteId())
                        ->loadByEmail($email)
                        ->getId();
                if ($ownerId !== null && $ownerId != $customerSession->getId()) {
                    Mage::throwException($this->__('This email address is already assigned to another user.'));
                }
                $subscriberModel = Mage::getModel('newsletter/subscriber');
                $subscriber = $subscriberModel->loadByEmail($email);
                if (!empty($subscriber->getSubscriberId()) && $subscriber->getStatus()) {
                  if ($subscriber->getStatus() == Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED) {
                    $subscriberModel->subscribe($email);
                    $response['success'] = true;
                    $response['message'] =  $this->__('Thank you for your subscription.');
                  }else{                    
                    $response['success'] = false;
                    $response['message'] =  $this->__('Already subscribed.');
                  }
                }else{
                  $status = $subscriberModel->subscribe($email);
                  if ($status == Mage_Newsletter_Model_Subscriber::STATUS_NOT_ACTIVE) {
                      $response['success'] = false;
                      $response['message'] =  $this->__('Confirmation request has been sent.');
                  }
                  else {
                      $response['success'] = true;
                      $response['message'] =  $this->__('Thank you for your subscription.');
                  }
                }
            }
            catch (Mage_Core_Exception $e) {
                $response['success'] = false;
                $response['message'] = $this->__('There was a problem with the subscription: %s', $e->getMessage());
            }
            catch (Exception $e) {
                  $response['success'] = false;
                  $response['message'] =  $this->__('There was a problem with the subscription.');
            }
        }
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($response));
        // $this->_redirectReferer();
    }
}
