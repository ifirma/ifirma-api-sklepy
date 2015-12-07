<?php

namespace ifirma;

require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceAbstract.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceContractor.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoicePosition.php';

/**
 * Description of StrategyAbstract
 *
 * @author bbojanowicz
 */
abstract class StrategyAbstract {
	
	const ORDER_NUMBER_PREFIX = "Zamówienie #";
	const SHIPPING_NAME_PREFIX = "Wysyłka - ";
	const DEFAULT_UNIT_NAME = 'szt.';
	const DEFAULT_SHIPPING_UNIT_NAME = 'usł.';
	
	protected $_order;
	protected $_address;
	protected $_customer;
	protected $_shipping;
	
	/**
	 * @return InvoiceAbstract
	 */
	public abstract function makeInvoice();

	/**
	 * 
	 * @param type $order
	 * @return \ifirma\StrategyAbstract
	 */
	public function setOrder($order){
		$this->_order = $order;
		$this->_shipping = $this->_order->getShipping();
		
		return $this;
	}
	
	/**
	 * 
	 * @param type $address
	 * @return \ifirma\StrategyAbstract
	 */
	public function setAddress($address){
		$this->_address = $address;
		
		return $this;
	}
	
	/**
	 * 
	 * @param type $customer
	 * @return \ifirma\StrategyAbstract
	 */
	public function setCustomer($customer){
		$this->_customer = $customer;
		
		return $this;
	}
	
	/**
	 * @return InvoiceContractor
	 */
	protected function _createContractorObject(){
		$contractor = new InvoiceContractor();


		
		// TODO: check if length is greater than 150 chars and split neme into two fields
		$contractor->{InvoiceContractor::KEY_NAZWA} = (
				empty($this->_customer->company)
				?
				$this->_customer->firstname . ' ' . $this->_customer->lastname
				:
				$this->_customer->company
				);
		
		$contractor->{InvoiceContractor::KEY_NAZWA2} =$this->_address->company;
		$contractor->{InvoiceContractor::KEY_OSOBA_FIZYCZNA} = empty($this->_customer->company);
		$contractor->{InvoiceContractor::KEY_KOD_POCZTOWY} = $this->_address->postcode;
		$contractor->{InvoiceContractor::KEY_MIEJSCOWOSC} = $this->_address->city;
		$contractor->{InvoiceContractor::KEY_NIP} = (!empty($this->_address->vat_number) ? $this->_address->vat_number : null);
		$contractor->{InvoiceContractor::KEY_ULICA} = $this->_address->address1 . ' ' . $this->_address->address2;
		$contractor->{InvoiceContractor::KEY_EMAIL} = $this->_customer->email;
		$contractor->{InvoiceContractor::KEY_KRAJ} = (!empty($this->_address->country) ? $this->_address->country : null);
		$contractor->{InvoiceContractor::KEY_TELEFON} = (
				empty($this->_address->phone_mobile)
				?
				(
					empty($this->_address->phone)
					?
					null
					:
					$this->_address->phone
				)
				:
				$this->_address->phone_mobile
				);
		
		return $contractor;
	}
	
	/**
	 * @return string
	 */
	protected function _getOrderNumber(){
		return sprintf("%s%s", self::ORDER_NUMBER_PREFIX, $this->_order->reference);
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function _getRawOrderNumber(){
		return $this->_order->reference;
	}
	
	/**
	 * @return string YYYY-MM-DD
	 */
	protected function _getOrderDate(){
		return preg_filter('#^(\d{4}-\d{1,2}-\d{1,2}).*#', '$1', $this->_order->date_add);
	}
	
	/**
	 * @return string
	 */
	protected function _getPaymentType(){
		switch($this->_order->module){
			case 'bankwire': 
				return 'PRZ';
				break;

			case 'cashondelivery': 
			case 'cashondeliverywithfee':
				return 'POB';
				break;

			case 'paypal': 
			case 'payu':
			case 'prestacafepayu':
			case 'platnoscipl': 
			case 'openpayu':
			case 'dotpay':
			case 'prestacafedotpay':
				return 'ELE';
				break;

			case 'cheque':
				return 'CZK';
				break;
		}
		
		return InvoiceAbstract::DEFAULT_VALUE_SPOSOB_ZAPLATY;
	}
	
	/**
	 * 
	 * @param array $product
	 * @return InvoicePosition
	 */
	protected function _createInvoicePosition(array $product){
		$invoicePosition = new InvoicePosition();
		
		$invoicePosition->{InvoicePosition::KEY_JEDNOSTKA} = (
				isset($product['unity']) && $product['unity'] != ''
				?
				$product['unity']
				:
				self::DEFAULT_UNIT_NAME
				);
		
		$invoicePosition->{InvoicePosition::KEY_ILOSC} = $product['product_quantity'];
		$invoicePosition->{InvoicePosition::KEY_CENA_JEDNOSTKOWA} = $this->_roundPrice($product['unit_price_tax_incl']);
		$invoicePosition->{InvoicePosition::KEY_NAZWA_PELNA} = substr($product['product_name'],0,300);
		if($this->_getConfig()->{Config::API_RYCZALT}){
			$invoicePosition->{InvoicePosition::KEY_RYCZALT_STAWKA} = $this->_getConfig()->{Config::API_RYCZALT_RATE};
		}
		
		if(!$this->_getConfig()->{Config::API_VAT}){
			$invoicePosition->{InvoicePosition::KEY_PODSTAWA_PRAWNA} = $this->_getConfig()->{Config::API_PODSTAWA_PRAWNA};
			$invoicePosition->{InvoicePosition::KEY_STAWKA_VAT} = null;
			$invoicePosition->{InvoicePosition::KEY_TYP_STAWKI_VAT} = InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT_ZW;
			$invoicePosition->{InvoicePosition::KEY_PKWiU} = "";
		}else{
			$invoicePosition->{InvoicePosition::KEY_STAWKA_VAT} = (string)($product['tax_rate']/100);
			$invoicePosition->{InvoicePosition::KEY_TYP_STAWKI_VAT} = InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT;
		}
		
		return $invoicePosition;
	}
	
	/**
	 * Max 2 digits after dot
	 * @param string|mixed $price
	 * @param int $precision
	 * @return string
	 */
	protected function _roundPrice($price, $precision = 2){
		$price = (string)$price;
		
		$dotPosition = strpos($price, '.');
		if(!$dotPosition){
			return $price;
		}
		
		return substr($price, 0, $dotPosition + $precision + 1);
	}
	
	/**
	 * 
	 * @return boolean
	 */
	protected function _isOrderWithoutShipping(){
		$cartRules = $this->_order->getCartRules();
		
		foreach($cartRules as $cartRule){
			if($cartRule['free_shipping'] == 1){
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * @return InvoicePosition
	 */
	protected function _createInvoicePositionShippingCost(){
		$invoicePosition = new InvoicePosition();
		
		$shipping = $this->_shipping[0];
		$tax = \Tax::getCarrierTaxRate($shipping['id_carrier'], $this->_order->id_address_delivery);
		
		$invoicePosition->{InvoicePosition::KEY_JEDNOSTKA} = self::DEFAULT_SHIPPING_UNIT_NAME;
		$invoicePosition->{InvoicePosition::KEY_ILOSC} = 1;
		$invoicePosition->{InvoicePosition::KEY_CENA_JEDNOSTKOWA} = $this->_roundPrice($shipping['shipping_cost_tax_incl']);
		$invoicePosition->{InvoicePosition::KEY_NAZWA_PELNA} = self::SHIPPING_NAME_PREFIX . $shipping['state_name'];
		
		if($this->_getConfig()->{Config::API_RYCZALT}){
			$invoicePosition->{InvoicePosition::KEY_RYCZALT_STAWKA} = $this->_getConfig()->{Config::API_RYCZALT_RATE};
		}
		if(!$this->_getConfig()->{Config::API_VAT}){
			$invoicePosition->{InvoicePosition::KEY_PODSTAWA_PRAWNA} = $this->_getConfig()->{Config::API_PODSTAWA_PRAWNA};
			$invoicePosition->{InvoicePosition::KEY_STAWKA_VAT} = null;
			$invoicePosition->{InvoicePosition::KEY_TYP_STAWKI_VAT} = InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT_ZW;
		}else{
			$invoicePosition->{InvoicePosition::KEY_STAWKA_VAT} = (string)($tax/100);
			$invoicePosition->{InvoicePosition::KEY_TYP_STAWKI_VAT} = InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT;
		}
		
		return $invoicePosition;
	}
	
	/**
	 * @return boolean
	 */
	protected function _isNecessaryToAddShippingPosition(){
		return !$this->_isOrderWithoutShipping() 
				&& count($this->_shipping) > 0 
				&& $this->_shipping[0]['shipping_cost_tax_incl'] > 0;
	}
	

	/**
	 * @return Conf
	 */
	protected function _getConfig(){
		return Config::getInstance();
	}
}

