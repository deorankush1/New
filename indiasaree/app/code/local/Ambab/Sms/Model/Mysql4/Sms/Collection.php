<?php

	class Ambab_Sms_Model_Mysql4_Sms_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
	{
		function _construct()
		{
			$this->_init('sms/sms');	
		}
	}

?>