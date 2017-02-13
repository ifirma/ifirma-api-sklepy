<?php

namespace ifirma;

require_once dirname(__FILE__) . '/InvoiceMap.php';
require_once dirname(__FILE__) . '/../connector/DataContainer.php';

/**
 * Description of InvoiceMapCollection
 *
 * @author bbojanowicz
 */
class InvoiceMapCollection extends DataContainer{
	
	/**
	 * 
	 * @param array $data
	 */
	public function __construct(array $data) {
		foreach($data as $row){
			$invoiceMapRow = $this->_createInvoiceMapObject($row);
			$this->{$invoiceMapRow->{InvoiceMap::COLUMN_NAME_INVOICE_TYPE}} = $invoiceMapRow;
		}
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getSupportedKeys() {
		return array(
			InvoiceMap::INVOICE_TYPE_BILL,
			InvoiceMap::INVOICE_TYPE_NORMAL,
			InvoiceMap::INVOICE_TYPE_PROFORMA,
			InvoiceMap::INVOICE_TYPE_SEND
		);
	}
	
	/**
	 * 
	 * @param array $row
	 * @return \ifirma\InvoiceMap
	 */
	private function _createInvoiceMapObject(array $row){
		$invoiceMap = new InvoiceMap();
		foreach($row as $key => $value){
			$invoiceMap->$key = $value;
		}
		return $invoiceMap;
	}
}

