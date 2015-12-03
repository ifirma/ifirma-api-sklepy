<?php

require_once dirname(__FILE__) . '/StrategyAbstract.php';
require_once dirname(__FILE__) . '/../connector/Invoice/Invoice.php';

/**
 * Description of StrategyInvoice
 *
 * @author bbojanowicz
 */
class StrategyInvoice extends StrategyAbstract{
	
	/**
	 * @return Invoice
	 */
	public function makeInvoice() {
                global $loader, $registry;
		$invoice = new Invoice();
		
		$invoice->{Invoice::KEY_KONTRAHENT} = $this->_createContractorObject();
		
		$invoice->{Invoice::KEY_SPOSOB_ZAPLATY} = $this->_getPaymentType();
		$invoice->{Invoice::KEY_LICZ_OD} = Invoice::DEFAULT_VALUE_LICZ_OD;
		$invoice->{Invoice::KEY_FORMAT_DATY_SPRZEDAZY} = Invoice::DEFAULT_VALUE_FORMAT_DATY_SPRZEDAZY;
		$invoice->{Invoice::KEY_DATA_WYSTAWIENIA} = date('Y-m-d');
		$invoice->{Invoice::KEY_DATA_SPRZEDAZY} = $this->_getOrderDate();
		$invoice->{Invoice::KEY_ZAPLACONO} = 0;
		$invoice->{Invoice::KEY_RODZAJ_PODPISU_ODBIORCY} = Invoice::DEFAULT_VALUE_RODZAJ_PODPISU_ODBIORCY;
		$invoice->{Invoice::KEY_UWAGI} = $this->_getOrderNumber();
		$invoice->{Invoice::KEY_WIDOCZNY_NUMER_GIOS} = Invoice::DEFAULT_VALUE_WIDOCZNY_NUMER_GIOS;
		$loader->model('sale/order');
                $order = $registry->get('model_sale_order');
		foreach($order->getOrderProducts($this->_order["order_id"]) as $product){                        
			$invoice->addInvoicePosition($this->_createInvoicePosition($product));
		}
		
		if($this->_isNecessaryToAddShippingPosition()){
			$invoice->addInvoicePosition($this->_createInvoicePositionShippingCost($this->_order["shipping_code"], $this->_order["shipping_country_id"], $this->_order["shipping_zone_id"]));
		}
		
		return $invoice;
	}
}

