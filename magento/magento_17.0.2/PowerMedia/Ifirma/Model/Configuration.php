<?php

require_once dirname(__FILE__) . '/Connector/IfirmaException.php';
require_once dirname(__FILE__) . '/Connector/Config.php';

/**
 * Description of Configuration
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Model_Configuration {
	
	private static $instance;
	
	private function __construct() {}
	
	/**
	 * 
	 * @return PowerMedia_Ifirma_Model_Configuration
	 */
	public static function getInstance(){
		if(self::$instance === null){
			self::$instance = new PowerMedia_Ifirma_Model_Configuration();
		}
		
		return self::$instance;
	}
	
	/**
	 * @return bool
	 */
	public function isDefined(){
		$apiLogin = Mage::getStoreConfig('ifirma_section/ifirma_api_keys/login_option');
		$subscriberKey = Mage::getStoreConfig('ifirma_section/ifirma_api_keys/subscriber_option');
		$invoiceKey = Mage::getStoreConfig('ifirma_section/ifirma_api_keys/invoice_option');
		$billKey = Mage::getStoreConfig('ifirma_section/ifirma_api_keys/bill_option');
		$vatOption = Mage::getStoreConfig('ifirma_section/ifirma_vat/vat_option');
		
		return (
			$apiLogin !== null
			&&
			$subscriberKey !== null
			&&
			$invoiceKey !== null
			&&
			$billKey !== null
			&&
			$vatOption !== null
		);
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isVAT(){
		if(!$this->isDefined()){
			throw new \ifirma\IfirmaException('No configuration defined');
		}
		
		return (bool)Mage::getStoreConfig('ifirma_section/ifirma_vat/vat_option');
	}
	
	/**
	 * 
	 * @return \PowerMedia_Ifirma_Model_Configuration
	 */
	public function setConfiguration(){
		if(!$this->isDefined()){
			throw new \ifirma\IfirmaException('No configuration defined');
		}
		
		\ifirma\Config::getInstance()->{\ifirma\Config::API_LOGIN} = Mage::getStoreConfig('ifirma_section/ifirma_api_keys/login_option');
		\ifirma\Config::getInstance()->{\ifirma\Config::API_KEY_SUBSCRIBER} = Mage::getStoreConfig('ifirma_section/ifirma_api_keys/subscriber_option');
		\ifirma\Config::getInstance()->{\ifirma\Config::API_KEY_INVOICE} = Mage::getStoreConfig('ifirma_section/ifirma_api_keys/invoice_option');
		\ifirma\Config::getInstance()->{\ifirma\Config::API_KEY_BILL} = Mage::getStoreConfig('ifirma_section/ifirma_api_keys/bill_option');
		
		return $this;
	}
}

