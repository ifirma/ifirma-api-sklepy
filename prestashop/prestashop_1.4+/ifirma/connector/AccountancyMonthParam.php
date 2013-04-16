<?php

namespace ifirma;

require_once dirname(__FILE__) . '/DataContainer.php';

/**
 * Description of AccountancyMonthParam
 *
 * @author bbojanowicz
 */
class AccountancyMonthParam extends DataContainer implements ToJsonInterface{
	
	const KEY_MONTH = 'MiesiacKsiegowy';
	const KEY_IMPORT_DATA_PREV_YEAR = 'PrzeniesDaneZPoprzedniegoRoku';
	
	const VALUE_MONTH_NEXT = 'NAST';
	const VALUE_MONTH_PREV = 'POPRZ';
	const DEFAULT_VALUE_IMPORT_DATA_PREV_YEAR = true;
	
	/**
	 * 
	 * @return array
	 */
	public function getSupportedKeys() {
		return array(
			self::KEY_MONTH,
			self::KEY_IMPORT_DATA_PREV_YEAR
		);
	}
	
	/**
	 * @return string
	 */
	public function toJson(){
		return json_encode(array(
			self::KEY_MONTH => $this->{self::KEY_MONTH},
			self::KEY_IMPORT_DATA_PREV_YEAR => $this->{self::KEY_IMPORT_DATA_PREV_YEAR}
		));
	}
	
	/**
	 * @return AccountancyMonthParam
	 */
	public static function getPrevMonthParam(){
		$param = new self();
		
		$param->{self::KEY_MONTH} = self::VALUE_MONTH_PREV;
		$param->{self::KEY_IMPORT_DATA_PREV_YEAR} = self::DEFAULT_VALUE_IMPORT_DATA_PREV_YEAR;
		
		return $param;
	}
	
	/**
	 * @return AccountancyMonthParam
	 */
	public static function getNextMonthParam(){
		$param = new self();
		
		$param->{self::KEY_MONTH} = self::VALUE_MONTH_NEXT;
		$param->{self::KEY_IMPORT_DATA_PREV_YEAR} = self::DEFAULT_VALUE_IMPORT_DATA_PREV_YEAR;
		
		return $param;
	}
}

