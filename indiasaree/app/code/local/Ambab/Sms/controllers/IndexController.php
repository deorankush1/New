<?php

	class Ambab_Sms_IndexController extends Mage_Core_Controller_Front_Action
	{
		public function indexAction()
		{
			$tp=Mage::getModel('core/date')->date('Y-m-d H:i:s');
			$otp = rand(1010,9999);
			echo $otp;
			$data = ['otp' => $otp,'created_at'=> $tp];
			$model= Mage::getModel('sms/sms');
            $id = 1;
            $model = Mage::getModel('sms/sms')->load($id)->addData($data);
            try {
               $model->save();
               echo "Data updated successfully.";

            } catch (Exception $e){
            echo $e->getMessage(); 
            }
            $phone = $this->getRequest()->getParams();
            $phone = $phone['mobile'];
            // $customer = Mage::getSingleton('customer/session')->getCustomer();
            // $phone = $customer->getMobileNumber();
            
            $templateVariables = array(
                'otp'=> $otp
            );
            $template = Mage::helper('ambab_sms')->getNewOrderTemplate();
            $content = self::renderTemplate($template, $templateVariables);
          self::sendSMS($phone,$content);
		
        $this->getResponse()->clearHeaders()->setHeader("Content-type","application/json");
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($phone));

		}

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
            //echo $url;
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
    public function getOtpByParamAction()
    {
        $sms= $this->getRequest()->getParams(); // sms entered by the user
                
        $result = Mage::getModel('sms/sms')->load($sms['otp'], 'otp');

        $current_timestamp = Mage::getModel('core/date')->date('Y-m-d H:i:s',strtotime('-1 minutes'));


        if($result['otp'] == $sms['otp']) {
            if($result['created_at'] >= $current_timestamp){
                $result = "Valid OTP";
            }
            else{
                $result = "*OTP expired";
            }
        }
        else{
            $result = "*Invalid OTP";
        }
        
        $this->getResponse()->clearHeaders()->setHeader('Content-type','application/json');
        
        $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
    }
}

?>