<?php

require_once dirname(__FILE__) . '/StrategyAbstract.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceSend.php';

/**
 * Description of StrategyInvoiceSend
 *
 * @author bbojanowicz
 */
class StrategyInvoiceSend extends StrategyAbstract{
	
	/**
	 * 
	 * @return \ifirma\InvoiceSend
	 */
	public function makeInvoice() {
                global $loader, $registry;
		$invoice = new InvoiceSend();
		
		$invoice->{InvoiceSend::KEY_KONTRAHENT} = $this->_createContractorObject();
		
		$invoice->{InvoiceSend::KEY_LICZ_OD} = InvoiceSend::DEFAULT_VALUE_LICZ_OD;
		$invoice->{InvoiceSend::KEY_FORMAT_DATY_SPRZEDAZY} = InvoiceSend::DEFAULT_VALUE_FORMAT_DATY_SPRZEDAZY;
		$invoice->{InvoiceSend::KEY_DATA_WYSTAWIENIA} = date('Y-m-d');
		$invoice->{InvoiceSend::KEY_DATA_SPRZEDAZY} = $this->_getOrderDate();
		$invoice->{InvoiceSend::KEY_ZAPLACONO} = 0;
		$invoice->{InvoiceSend::KEY_RODZAJ_PODPISU_ODBIORCY} = InvoiceSend::DEFAULT_VALUE_RODZAJ_PODPISU_ODBIORCY;
		$invoice->{InvoiceSend::KEY_UWAGI} = $this->_getOrderNumber();
		$invoice->{InvoiceSend::KEY_WIDOCZNY_NUMER_GIOS} = InvoiceSend::DEFAULT_VALUE_WIDOCZNY_NUMER_GIOS;
		
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

