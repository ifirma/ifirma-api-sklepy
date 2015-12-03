<?php

use ifirma\Config;
require_once dirname(__FILE__) . '/../Connector/Invoice/InvoiceAbstract.php';
require_once dirname(__FILE__) . '/../Connector/Invoice/InvoiceContractor.php';
require_once dirname(__FILE__) . '/../Connector/Invoice/InvoicePosition.php';
require_once dirname(__FILE__) . '/../Connector/Config.php';
require_once dirname(__FILE__) . '/../Vat.php';

/**
 * Description of StrategyAbstract
 *
 * @author bbojanowicz
 */
abstract class PowerMedia_Ifirma_Model_Builder_StrategyAbstract {
	
	const ORDER_NUMBER_PREFIX = "Zamówienie #";
	const SHIPPING_NAME_PREFIX = "Wysyłka - ";
	const DEFAULT_UNIT_NAME = 'szt.';
	const DEFAULT_SHIPPING_UNIT_NAME = 'usł.';
	
	protected $order;
	
	/**
	 * @return InvoiceAbstract
	 */
	public abstract function makeInvoice();
	
	public function setOrder($order){
		$this->order = $order;
		
		return $this;
	}
	
	public function getOrder(){
		return $this->order;
	}
	
	/**
	 * Max 2 digits after dot
	 * @param string|mixed $price
	 * @param int $precision
	 * @return string
	 */
	protected function roundPrice($price, $precision = 2){
		$price = (string)$price;
		
		$dotPosition = strpos($price, '.');
		if(!$dotPosition){
			return $price;
		}
		
		return substr($price, 0, $dotPosition + $precision + 1);
	}
	
	/**
	 * 
	 * @return \ifirma\InvoiceContractor
	 */
	protected function createContractorObject(){ 
		$contractor = new ifirma\InvoiceContractor();
		
		$contractor->{ifirma\InvoiceContractor::KEY_NAZWA} = $this->getOrder()->getBillingAddress()->getName();
		$contractor->{ifirma\InvoiceContractor::KEY_NAZWA2} = $this->getOrder()->getBillingAddress()->getCompany();
		$contractor->{ifirma\InvoiceContractor::KEY_KOD_POCZTOWY} = $this->getContractorFormatedPostCode();
		$contractor->{ifirma\InvoiceContractor::KEY_MIEJSCOWOSC} = $this->getOrder()->getBillingAddress()->getCity();
		$contractor->{ifirma\InvoiceContractor::KEY_NIP} = $this->getOrder()->getBillingAddress()->getVatId();
		$contractor->{ifirma\InvoiceContractor::KEY_ULICA} = $this->getOrder()->getBillingAddress()->getStreetFull();
		$contractor->{ifirma\InvoiceContractor::KEY_TELEFON} = $this->getOrder()->getBillingAddress()->getTelephone();
		$country_id = $this->getOrder()->getBillingAddress()->getCountry();
		$country_name=Mage::app()->getLocale()->getCountryTranslation($country_id);
		$contractor->{ifirma\InvoiceContractor::KEY_KRAJ} = $country_name;
		
		return $contractor;
	}
	
	/**
	 * 
	 * @param type $item
	 * @return \ifirma\InvoicePosition
	 */
	protected function createInvoicePosition($item){
		$invoicePosition = new ifirma\InvoicePosition();
		
		$invoicePosition->{ifirma\InvoicePosition::KEY_JEDNOSTKA} = self::DEFAULT_UNIT_NAME;
		$invoicePosition->{ifirma\InvoicePosition::KEY_ILOSC} = $item->getQtyOrdered();
		$invoicePosition->{ifirma\InvoicePosition::KEY_CENA_JEDNOSTKOWA} = $this->roundPrice($item->getPriceInclTax());
		$invoicePosition->{ifirma\InvoicePosition::KEY_NAZWA_PELNA} = $item->getName();
		
		if($this->_getConfig()->{Config::API_RYCZALT}){
			$invoicePosition->{ifirma\InvoicePosition::KEY_RYCZALT_STAWKA} = $this->_getConfig()->{Config::API_RYCZALT_RATE};
		}
		print_r($this->_getConfig()->{Config::API_RYCZALT});
		print_r($this->_getConfig()->{Config::API_VAT});
		if(!$this->_getConfig()->{Config::API_VAT}){
			//print_r("NIE VAT");
			$invoicePosition->{ifirma\InvoicePosition::KEY_PODSTAWA_PRAWNA} = $this->_getConfig()->{Config::API_PODSTAWA_PRAWNA};
			$invoicePosition->{ifirma\InvoicePosition::KEY_STAWKA_VAT} = null;
			$invoicePosition->{ifirma\InvoicePosition::KEY_TYP_STAWKI_VAT} = ifirma\InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT_ZW;
		}else{
			//print_r("VAT");
			$invoicePosition->{ifirma\InvoicePosition::KEY_STAWKA_VAT} = sprintf("%.2f", $item->getTaxPercent()/100);			
			$invoicePosition->{ifirma\InvoicePosition::KEY_TYP_STAWKI_VAT} = ifirma\InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT;
			$invoicePosition->{ifirma\InvoicePosition::KEY_PKWiU} = "";
		}
		return $invoicePosition;
	}
	
	/**
	 * 
	 * @return \ifirma\InvoicePosition
	 */
	protected function createInvoicePositionShippingCost(){
		$invoicePosition = new ifirma\InvoicePosition();
		
		$invoicePosition->{ifirma\InvoicePosition::KEY_STAWKA_VAT} = $this->roundPrice($this->calculateShippingTaxPercentValue());
		$invoicePosition->{ifirma\InvoicePosition::KEY_JEDNOSTKA} = self::DEFAULT_SHIPPING_UNIT_NAME;
		$invoicePosition->{ifirma\InvoicePosition::KEY_ILOSC} = 1;
		$invoicePosition->{ifirma\InvoicePosition::KEY_CENA_JEDNOSTKOWA} = $this->roundPrice($this->getOrder()->getShippingInclTax());
		$invoicePosition->{ifirma\InvoicePosition::KEY_TYP_STAWKI_VAT} = ifirma\InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT;
		$invoicePosition->{ifirma\InvoicePosition::KEY_NAZWA_PELNA} = self::SHIPPING_NAME_PREFIX . $this->getOrder()->getShippingDescription();
		
		if($this->_getConfig()->{ifirma\Config::API_RYCZALT}){
			$invoicePosition->{ifirma\InvoicePosition::KEY_RYCZALT_STAWKA} = $this->_getConfig()->{ifirma\Config::API_RYCZALT_RATE};
		}
		if(!$this->_getConfig()->{ifirma\Config::API_VAT}){
			$invoicePosition->{ifirma\InvoicePosition::KEY_PODSTAWA_PRAWNA} = $this->_getConfig()->{ifirma\Config::API_PODSTAWA_PRAWNA};
			$invoicePosition->{ifirma\InvoicePosition::KEY_STAWKA_VAT} = null;
			$invoicePosition->{ifirma\InvoicePosition::KEY_TYP_STAWKI_VAT} = ifirma\InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT_ZW;
		}else{
			$shipping_tax_amount = $this->getOrder()->getShippingTaxAmount();
			$shipping_amount = $this->getOrder()->getShippingAmount();
			$shipping_incl_tax = $this->getOrder()->getShippingInclTax();
			$taxPercent = $shipping_tax_amount / $shipping_amount;
			$invoicePosition->{ifirma\InvoicePosition::KEY_STAWKA_VAT} = sprintf("%.2f", $taxPercent);
			$invoicePosition->{ifirma\InvoicePosition::KEY_TYP_STAWKI_VAT} = ifirma\InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT;
			$invoicePosition->{ifirma\InvoicePosition::KEY_PKWiU} = "";
		}
		return $invoicePosition;
	}
	
	/**
	 * 
	 * @return float
	 */
	private function calculateShippingTaxPercentValue(){
        $shippingTax = $this->getOrder()->getShippingTaxAmount() / $this->getOrder()->getShippingAmount();
        $currentTax = $this->_getVat()->currentVat($shippingTax);
        return $currentTax;
	}
	
	/**
	 * 
	 * @return bool
	 */
	protected function isNecessaryToAddShippingPosition(){
		return floatval($this->getOrder()->getShippingInclTax()) > 0;		
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function getContractorFormatedPostCode(){
		$rawPostCode = $this->getOrder()->getBillingAddress()->getPostcode();
		
		if(preg_match('#^\d{2}-\d{3}$#', $rawPostCode)){
			return $rawPostCode;
		}else {
			return sprintf("%s-%s",substr($rawPostCode, 0, 2),  substr($rawPostCode, 2));
		}
	}
	
	/**
	 * @return string YYYY-MM-DD
	 */
	protected function getOrderDate(){
		return $this->getOrder()->getCreatedAtFormated()->get('YYYY-MM-dd');
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function getOrderNumber(){
		return sprintf("%s%s", self::ORDER_NUMBER_PREFIX, $this->getRawOrderNumber());
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function getRawOrderNumber(){
		return $this->getOrder()->getRealOrderId();
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function getPaymentType(){
		switch($this->getOrder()->getPayment()->getMethodInstance()->getCode()){
			case 'moneybookers_pwy':
			case 'banktransfer':
				return 'PRZ';
				break;
			
			case 'paypal_billing_agreement':
			case 'paypal_express':
			case 'paypaluk_express':
			case 'paypal_mecl':	
			case 'paypal_mep':
			case 'paypal_direct':
			case 'paypaluk_direct':
			case 'paypal_standard':
				return 'ELE';
				break;
			
			case 'checkmo':
				return 'CZK';
				break;
			
			case 'cashondelivery':
				return 'POB';
				break;
			
			case 'payflow_link':
			case 'payflow_advanced':
			case 'authorizenet':
			case 'ccsave':
			case 'moneybookers_acc':
			case 'authorizenet_directpost':
				return 'KAR';
				break;
			
			default:
				return ifirma\InvoiceAbstract::DEFAULT_VALUE_SPOSOB_ZAPLATY;
				break;
		}
	}
	

	/**
	 * @return Conf
	 */
	protected function _getConfig(){
		//Config::getInstance();
		return ifirma\Config::getInstance();
	}
	
	/**
	 * @return Vat 
	 */
	protected function _getVat(){
		return ifirma\Vat::getInstance();
	}

}

