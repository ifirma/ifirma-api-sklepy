<?php

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
                global $loader, $registry;
		$invoice = new InvoiceBill();
		
		$invoice->{InvoiceBill::KEY_KONTRAHENT} = $this->_createContractorObject();
		
		$invoice->{InvoiceBill::KEY_FORMAT_DATY_SPRZEDAZY} = InvoiceBill::DEFAULT_VALUE_FORMAT_DATY_SPRZEDAZY;
		$invoice->{InvoiceBill::KEY_DATA_WYSTAWIENIA} = date('Y-m-d');
		$invoice->{InvoiceBill::KEY_DATA_SPRZEDAZY} = $this->_getOrderDate();
		$invoice->{InvoiceBill::KEY_ZAPLACONO} = 0;
		$invoice->{InvoiceBill::KEY_WPIS_DO_KPIR} = InvoiceBill::DEFAULT_VALUE_WPIS_DO_KPIR;
		$invoice->{InvoiceBill::KEY_UWAGI} = $this->_getOrderNumber();
		$invoice->{InvoiceBill::KEY_SPOSOB_ZAPLATY} = $this->_getPaymentType();
		
		$loader->model('sale/order');
                $order = $registry->get('model_sale_order');
		foreach($order->getOrderProducts($this->_order["order_id"]) as $product){
			$invoice->addInvoiceBillPosition($this->_createInvoiceBillPosition($product));
		}
		
		if($this->_isNecessaryToAddShippingPosition()){
			$invoice->addInvoiceBillPosition($this->_createInvoiceBillPositionShippingCost($this->_order["shipping_code"], $this->_order["shipping_country_id"], $this->_order["shipping_zone_id"]));
		}
		
		return $invoice;
	}
	
	/**
	 * @return invoiceBillPosition
	 */
	private function _createInvoiceBillPositionShippingCost($shippingCode, $shippingCountryId, $shippingZoneId){      
		global $loader, $registry;
                $loader->model('module/invoice_map');
                $invoiceModel = $registry->get('model_module_invoice_map');
		$tax = $invoiceModel->getShippingTax($shippingCode, $shippingCountryId, $shippingZoneId, 'bill');
                        
                $invoiceBillPosition = new InvoiceBillPosition();
		
		$shipping = $this->_shipping;
				
		$invoiceBillPosition->{InvoiceBillPosition::KEY_JEDNOSTKA} = self::DEFAULT_SHIPPING_UNIT_NAME;
		$invoiceBillPosition->{InvoiceBillPosition::KEY_ILOSC} = 1;
		$invoiceBillPosition->{InvoiceBillPosition::KEY_CENA_JEDNOSTKOWA} = $this->_roundPrice($shipping['value']+$shipping['value']*$tax/100);
		$invoiceBillPosition->{InvoiceBillPosition::KEY_NAZWA_PELNA} = self::SHIPPING_NAME_PREFIX . $shipping['title'];
		
		return $invoiceBillPosition;
	}
	
	/**
	 * 
	 * @param array $product
	 * @return InvoiceBillPosition
	 */
	private function _createInvoiceBillPosition(array $product){
                global $loader, $registry;
                $loader->model('module/invoice_map');
                $invoiceModel = $registry->get('model_module_invoice_map');
		$tax = $invoiceModel->getProductTax($product['product_id'], 'bill');
		
                $invoiceBillPosition = new InvoiceBillPosition();
		
		$invoiceBillPosition->{InvoiceBillPosition::KEY_JEDNOSTKA} = (
				isset($product['unity']) && $product['unity'] != ''
				?
				$product['unity']
				:
				self::DEFAULT_UNIT_NAME
				);
		$invoiceBillPosition->{InvoiceBillPosition::KEY_ILOSC} = $product['quantity'];
		$invoiceBillPosition->{InvoiceBillPosition::KEY_CENA_JEDNOSTKOWA} = $this->_roundPrice($product['price']+$product['price']*$tax/100);
		$invoiceBillPosition->{InvoiceBillPosition::KEY_NAZWA_PELNA} = $product['name'];
				
		return $invoiceBillPosition;
	}
}