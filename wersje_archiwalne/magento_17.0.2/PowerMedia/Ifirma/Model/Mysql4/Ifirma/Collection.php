<?php

/**
 * Description of Collection
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Model_Mysql4_Ifirma_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract{
	
	protected function _construct(){
		$this->_init('ifirma/ifirma', 'id');
	}
}

