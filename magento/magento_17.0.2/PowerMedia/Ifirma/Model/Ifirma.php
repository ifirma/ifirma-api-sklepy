<?php

require_once dirname(__FILE__) . '/Connector/Filters/InvoiceNumberFilter.php';

/**
 * Description of Ifirma
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Model_Ifirma extends Mage_Core_Model_Abstract{
	
	const TYPE_INVOICE = 'invoice';
	const TYPE_INVOICE_SEND = 'invoice_send';
	const TYPE_INVOICE_PROFORMA = 'invoice_proforma';
	const TYPE_INVOICE_BILL = 'invoice_bill';
	
	protected function _construct(){
		$this->_init('ifirma/ifirma');
	}
	
	public function getFilteredNumber(){
		$filter = new ifirma\InvoiceNumberFilter();
		
		return $filter->filter($this->getInvoiceNumber());
	}
}

