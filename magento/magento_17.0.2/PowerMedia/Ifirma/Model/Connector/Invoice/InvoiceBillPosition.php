<?php

namespace ifirma;

require_once dirname(__FILE__) . '/InvoicePosition.php';

/**
 * Description of InvoiceBillPosition
 *
 * @author bbojanowicz
 */
class InvoiceBillPosition extends InvoicePosition{
	
	/**
	 * 
	 * @return array
	 */
	public function getSupportedKeys() {
		return array(
			self::KEY_CENA_JEDNOSTKOWA,
			self::KEY_ILOSC,
			self::KEY_JEDNOSTKA,
			self::KEY_NAZWA_PELNA,
			self::KEY_RABAT,
			self::KEY_CENA_Z_RABATEM
		);
	}
	
	/**
	 * 
	 * @return array
	 */
	public function toArray(){
		return array(
			self::KEY_CENA_JEDNOSTKOWA => $this->{self::KEY_CENA_JEDNOSTKOWA},
			self::KEY_ILOSC => $this->{self::KEY_ILOSC},
			self::KEY_JEDNOSTKA => $this->{self::KEY_JEDNOSTKA},
			self::KEY_NAZWA_PELNA => $this->{self::KEY_NAZWA_PELNA},
			self::KEY_RABAT => $this->{self::KEY_RABAT},
		);
	}
	
	/**
	 * 
	 * @param array $keys
	 * @return array
	 */
	public function filterKeys($keys){
		return self::filterSupportedKeys($this, $keys);	
	}
}

