<?php

require_once dirname(__FILE__) . '/invoice_map.php';
require_once dirname(__FILE__) . '/../../../connector/DataContainer.php';

class InvoiceMapCollection extends DataContainer{
	
	/**
	 * 
	 * @param array $data
	 */
	public function __construct(array $data) {
		foreach($data as $row){
			$this->{$row[ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_TYPE]} = $this->_createInvoiceMapObject($row);                     
                }
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getSupportedKeys() {
		return array(
			ModelModuleInvoiceMap::INVOICE_TYPE_BILL,
			ModelModuleInvoiceMap::INVOICE_TYPE_NORMAL,
			ModelModuleInvoiceMap::INVOICE_TYPE_PROFORMA,
			ModelModuleInvoiceMap::INVOICE_TYPE_SEND
		);
	}
	
	/**
	 * 
	 * @param array $row
	 * @return array
	 */
	private function _createInvoiceMapObject(array $row){
		foreach($row as $key => $value){
			$invoiceMap[$key] = $value;
		}
		return $invoiceMap;
	}
}