<?php

namespace ifirma;

require_once dirname(__FILE__) . '/FilterInterface.php';

/**
 * Description of InvoiceNumberFilter
 *
 * @author bbojanowicz
 */
class InvoiceNumberFilter implements FilterInterface{
	
	/**
	 * 
	 * @param string $value
	 * @return string
	 */
	public function filter($value){
		return preg_replace('#/#', '_', $value);
	}
}

