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
	
	const API_KEY_BILL = 'api-key-bill';
	const API_KEY_BILL_NAME = 'rachunek';
	const API_KEY_INVOICE = 'api-key-invoice';
	const API_KEY_INVOICE_NAME = 'faktura';
	const API_KEY_SUBSCRIBER = 'api-key-subscriber';
	const API_KEY_SUBSCRIBBER_NAME = 'abonent';
	const API_LOGIN = 'api-login';
	
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
			self::API_KEY_BILL,
			self::API_KEY_INVOICE,
			self::API_KEY_SUBSCRIBER,
			self::API_LOGIN
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

