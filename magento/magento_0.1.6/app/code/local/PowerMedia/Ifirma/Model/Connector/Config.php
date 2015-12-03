<?php

namespace ifirma;

require_once dirname(__FILE__) . '/IfirmaException.php';
require_once dirname(__FILE__) . '/DataContainer.php';

/**
 * Description of Config
 *
 * @author bbojanowicz
 */
class Config extends DataContainer{
	
	const API_VAT = 'api-vat';
	const API_KEY_BILL = 'api-key-bill';
	const API_KEY_BILL_NAME = 'rachunek';
	const API_KEY_INVOICE = 'api-key-invoice';
	const API_KEY_INVOICE_NAME = 'faktura';
	const API_KEY_SUBSCRIBER = 'api-key-subscriber';
	const API_KEY_SUBSCRIBBER_NAME = 'abonent';
	const API_LOGIN = 'api-login';

	const API_RYCZALT = 'api-key-ryczalt';
	const API_RYCZALT_NAME = 'ryczalt';
	const API_RYCZALT_RATE = 'api-key-ryczalt-rate';
	const API_RYCZALT_RATE_NAME = 'stawka ryczaltu';
	const API_RYCZALT_WPIS_DO_EWIDENCJI = 'api-key-ryczalt-wpis-do-ewidencji';
	const API_RYCZALT_WPIS_DO_EWIDENCJI_NAME = 'wpis do ewidencji';
	
	const API_PODSTAWA_PRAWNA = 'api-key-podstawa-prawna';
	const API_PODSTAWA_PRAWNA_NAME = 'podstawa prawna';
	
	const API_MIEJSCE_WYSTAWIENIA = 'api-key-miejsce-wystawienia';
	const API_MIEJSCE_WYSTAWIENIA_NAME = 'miejsce wystawienia';
	
	const API_NAZWA_SERII_NUMERACJI = 'api-key-nazwa-serii-numeracji';
	const API_NAZWA_SERII_NUMERACJI_NAME = 'nazwa serii numeracji';
	
	
	/**
	 *
	 * @var Config
	 */
	private static $_instance;
	
	private function __construct(){
		// do nothing
	}
	
	/**
	 * 
	 * @return Config
	 */
	public static function getInstance(){
		if(self::$_instance === null){
			self::$_instance = new self();
		}
		
		return self::$_instance;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getSupportedKeys(){
		return array(
			self::API_VAT,
			self::API_KEY_BILL,
			self::API_KEY_INVOICE,
			self::API_KEY_SUBSCRIBER,
			self::API_LOGIN,
			self::API_RYCZALT,
			self::API_RYCZALT_RATE,
			self::API_RYCZALT_WPIS_DO_EWIDENCJI,
			self::API_PODSTAWA_PRAWNA,
 			self::API_MIEJSCE_WYSTAWIENIA,
			self::API_NAZWA_SERII_NUMERACJI
		);
	}

	/**
	 * @return bool
	 */
	public function allDataSet(){
		return
			isset($this->_values[self::API_KEY_BILL])
			&&
			isset($this->_values[self::API_KEY_INVOICE])
			&&
			isset($this->_values[self::API_KEY_SUBSCRIBER])
			&&
			isset($this->_values[self::API_LOGIN])
			;
	}
}

