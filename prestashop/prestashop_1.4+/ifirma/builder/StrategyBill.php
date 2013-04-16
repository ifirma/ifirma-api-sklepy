<?php

namespace ifirma;

require_once dirname(__FILE__) . '/StrategyAbstract.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceBill.php';

/**
 * Description of StrategyBill
 *
 * @author bbojanowicz
 */
class StrategyBill extends StrategyAbstract{
	
	/**
	 * 
	 * @return \ifirma\InvoiceBill
	 */
	public function makeInvoice() {
		$invoice = new InvoiceBill();
		
		$invoice->{InvoiceBill::KEY_KONTRAHENT} = $this->_createContractorObject();
		
		$invoice->{InvoiceBill::KEY_FORMAT_DATY_SPRZEDAZY} = InvoiceBill::DEFAULT_VALUE_FORMAT_DATY_SPRZEDAZY;
		$invoice->{InvoiceBill::KEY_DATA_WYSTAWIENIA} = date('Y-m-d');
		$invoice->{InvoiceBill::KEY_DATA_SPRZEDAZY} = $this->_getOrderDate();
		$invoice->{InvoiceBill::KEY_ZAPLACONO} = 0;
		$invoice->{InvoiceBill::KEY_WPIS_DO_KPIR} = InvoiceBill::DEFAULT_VALUE_WPIS_DO_KPIR;
		$invoice->{InvoiceBill::KEY_UWAGI} = $this->_getOrderNumber();
		$invoice->{InvoiceBill::KEY_SPOSOB_ZAPLATY} = $this->_getPaymentType();
		
		foreach($this->_order->getProducts() as $product){
			$invoice->addInvoiceBillPosition($this->_createInvoiceBillPosition($product));
		}
		
		if($this->_isNecessaryToAddShippingPosition()){
			$invoice->addInvoiceBillPosition($this->_createInvoiceBillPositionShippingCost());
		}
		
		return $invoice;
	}
	
	/**
	 * @return invoiceBillPosition
	 */
	private function _createInvoiceBillPositionShippingCost(){
		$invoiceBillPosition = new InvoiceBillPosition();
		
		$shipping = $this->_shipping[0];
				
		$invoiceBillPosition->{InvoiceBillPosition::KEY_JEDNOSTKA} = self::DEFAULT_SHIPPING_UNIT_NAME;
		$invoiceBillPosition->{InvoiceBillPosition::KEY_ILOSC} = 1;
		$invoiceBillPosition->{InvoiceBillPosition::KEY_CENA_JEDNOSTKOWA} = $this->_roundPrice($shipping['shipping_cost_tax_incl']);
		$invoiceBillPosition->{InvoiceBillPosition::KEY_NAZWA_PELNA} = self::SHIPPING_NAME_PREFIX . $shipping['state_name'];
		
		return $invoiceBillPosition;
	}
	
	/**
	 * 
	 * @param array $product
	 * @return InvoiceBillPosition
	 */
	private function _createInvoiceBillPosition(array $product){
		$invoiceBillPosition = new InvoiceBillPosition();
		
		$invoiceBillPosition->{InvoiceBillPosition::KEY_JEDNOSTKA} = (
				isset($product['unity']) && $product['unity'] != ''
				?
				$product['unity']
				:
				self::DEFAULT_UNIT_NAME
				);
		$invoiceBillPosition->{InvoiceBillPosition::KEY_ILOSC} = $product['product_quantity'];
		$invoiceBillPosition->{InvoiceBillPosition::KEY_CENA_JEDNOSTKOWA} = $this->_roundPrice($product['unit_price_tax_incl']);
		$invoiceBillPosition->{InvoiceBillPosition::KEY_NAZWA_PELNA} = $product['product_name'];
				
		return $invoiceBillPosition;
	}
}

