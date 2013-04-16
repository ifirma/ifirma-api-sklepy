<?php

namespace ifirma;

require_once dirname(__FILE__) . '/../IfirmaException.php';
require_once dirname(__FILE__) . '/../DataContainer.php';
require_once dirname(__FILE__) . '/../ToJsonInterface.php';
require_once dirname(__FILE__) . '/../ToArrayInterface.php';

/**
 * Description of InvoicePosition
 *
 * @author bbojanowicz
 */
class InvoicePosition extends DataContainer implements ToArrayInterface, ToJsonInterface{
	
	const KEY_STAWKA_VAT = 'StawkaVat';
	const KEY_ILOSC = 'Ilosc';
	const KEY_CENA_JEDNOSTKOWA = 'CenaJednostkowa';
	const KEY_NAZWA_PELNA = 'NazwaPelna';
	const KEY_JEDNOSTKA = 'Jednostka';
	const KEY_PKWiU = 'PKWiU';
	const KEY_TYP_STAWKI_VAT = 'TypStawkiVat';
	const KEY_RABAT = 'Rabat';
	
	// only for response object
	const KEY_CENA_Z_RABATEM = 'CenaZRabatem';
	
	const DEFAULT_VALUE_TYP_STAWKI_VAT = 'PRC';
	
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
			self::KEY_PKWiU,
			self::KEY_RABAT,
			self::KEY_STAWKA_VAT,
			self::KEY_TYP_STAWKI_VAT,
			self::KEY_CENA_Z_RABATEM
		);
	}
	
	/**
	 * @return string
	 */
	public function toJson(){
		return json_encode($this->toArray());
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
			self::KEY_PKWiU => $this->{self::KEY_PKWiU},
			self::KEY_RABAT => $this->{self::KEY_RABAT},
			self::KEY_STAWKA_VAT => $this->{self::KEY_STAWKA_VAT},
			self::KEY_TYP_STAWKI_VAT => $this->{self::KEY_TYP_STAWKI_VAT}
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

