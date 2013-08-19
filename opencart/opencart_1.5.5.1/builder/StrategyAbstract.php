<?php

require_once dirname(__FILE__) . '/../admin/model/sale/order.php';
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
        protected $_shipping;
	
	/**
	 * @return InvoiceAbstract
	 */
	public abstract function makeInvoice();

	/**
	 * 
	 * @param $order
	 * @return StrategyAbstract
	 */
	public function setOrder($order){
                global $loader, $registry;
                $loader->model('module/invoice_map');
                $invoiceModel = $registry->get('model_module_invoice_map');
		$this->_order = $order;
                $this->_shipping = $invoiceModel->getOrderShippingCost($order["order_id"]);
	}        
	
	/**
	 * @return InvoiceContractor
	 */
	protected function _createContractorObject(){
		$contractor = new InvoiceContractor();
		
		$contractor->{InvoiceContractor::KEY_NAZWA} = (
				empty($this->_order["payment_company"])
				?
				$this->_order["payment_firstname"] . ' ' . $this->_order["payment_lastname"]
				:
				$this->_order["payment_company"]
				);
		
		$contractor->{InvoiceContractor::KEY_OSOBA_FIZYCZNA} = empty($this->_order["payment_company"]);
		$contractor->{InvoiceContractor::KEY_KOD_POCZTOWY} = $this->_order["payment_postcode"];
		$contractor->{InvoiceContractor::KEY_MIEJSCOWOSC} = $this->_order["payment_city"];
		$contractor->{InvoiceContractor::KEY_NIP} = (!empty($this->_order["payment_company_id"]) ? $this->_order["payment_company_id"] : null);
		$contractor->{InvoiceContractor::KEY_ULICA} = $this->_order["payment_address_1"] . ' ' . $this->_order["payment_address_2"];
                $contractor->{InvoiceContractor::KEY_TELEFON} = (
				empty($this->_order["telephone"])
				?
				null
				:
				$this->_order["telephone"]
				);
		
		return $contractor;
	}
	
	/**
	 * @return string
	 */
	protected function _getOrderNumber(){
		return sprintf("%s%s", self::ORDER_NUMBER_PREFIX, $this->_order["order_id"]);
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function _getRawOrderNumber(){
		return $this->_order["order_id"];
	}
	
	/**
	 * @return string YYYY-MM-DD
	 */
	protected function _getOrderDate(){
		return preg_filter('#^(\d{4}-\d{1,2}-\d{1,2}).*#', '$1', $this->_order["date_added"]);
	}
	
	/**
	 * @return string
	 */
	protected function _getPaymentType(){
		switch($this->_order["payment_code"]){
			case 'pp_pro': 
                        case 'pp_standard':
				return 'PAL';
				break;

			case 'cod':
				return 'POB';
				break;
			case 'payu':
			case 'platnoscipl': 
			case 'openpayu':
				return 'ALG';
				break;

			case 'dotpay':
				return 'DOT';
				break;

			case 'cheque':
				return 'CZK';
				break;
                        case 'paypoint':
                        case 'sagepay':
                        case 'worldpay':
                        case 'web_payment_software':
                        case 'bank_transfer':                        
                            return 'PRZ';
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
                global $loader, $registry;
                $loader->model('module/invoice_map');
                $invoiceModel = $registry->get('model_module_invoice_map');
		$tax = $invoiceModel->getProductTax($product['product_id'], 'invoice');

      		$invoicePosition = new InvoicePosition();
		$invoicePosition->{InvoicePosition::KEY_JEDNOSTKA} = (
				isset($product['unity']) && $product['unity'] != ''
				?
				$product['unity']
				:
				self::DEFAULT_UNIT_NAME
				);
		$invoicePosition->{InvoicePosition::KEY_STAWKA_VAT} = (string)($tax/100);
		$invoicePosition->{InvoicePosition::KEY_ILOSC} = $product['quantity'];
		$invoicePosition->{InvoicePosition::KEY_CENA_JEDNOSTKOWA} = $this->_roundPrice($product['price']+$product['price']*$tax/100);
		$invoicePosition->{InvoicePosition::KEY_NAZWA_PELNA} = $product['name'];
		$invoicePosition->{InvoicePosition::KEY_TYP_STAWKI_VAT} = InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT;
		
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
		if (empty($this->_order["shipping_code"]) || $this->_order["shipping_code"] == 'free.free'){
                    return true;
                }else{
                    return false;
                }
	}
	
	/**
	 * @return InvoicePosition
	 */
	protected function _createInvoicePositionShippingCost($shippingCode, $shippingCountryId, $shippingZoneId){
                global $loader, $registry;
                $loader->model('module/invoice_map');
                $invoiceModel = $registry->get('model_module_invoice_map');
		$tax = $invoiceModel->getShippingTax($shippingCode, $shippingCountryId, $shippingZoneId, 'invoice');
                        
                $invoicePosition = new InvoicePosition();
		
		$shipping = $this->_shipping;
		
		$invoicePosition->{InvoicePosition::KEY_JEDNOSTKA} = self::DEFAULT_SHIPPING_UNIT_NAME;
		$invoicePosition->{InvoicePosition::KEY_STAWKA_VAT} = (string)($tax/100);
		$invoicePosition->{InvoicePosition::KEY_ILOSC} = 1;
		$invoicePosition->{InvoicePosition::KEY_CENA_JEDNOSTKOWA} = $this->_roundPrice($shipping['value']+$shipping['value']*$tax/100);
		$invoicePosition->{InvoicePosition::KEY_NAZWA_PELNA} = self::SHIPPING_NAME_PREFIX . $shipping['title'];
		$invoicePosition->{InvoicePosition::KEY_TYP_STAWKI_VAT} = InvoicePosition::DEFAULT_VALUE_TYP_STAWKI_VAT;
		
		return $invoicePosition;
	}
	
	/**
	 * @return boolean
	 */
	protected function _isNecessaryToAddShippingPosition(){
		return !$this->_isOrderWithoutShipping();
	}
}

