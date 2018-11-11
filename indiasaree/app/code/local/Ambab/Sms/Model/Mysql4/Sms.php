<?php

	class Ambab_Sms_Model_Mysql4_Sms extends Mage_Core_Model_Mysql4_Abstract
	{
		function _construct()
		{
			$this->_init('sms/sms','otp_id');	
		}
	}

?>