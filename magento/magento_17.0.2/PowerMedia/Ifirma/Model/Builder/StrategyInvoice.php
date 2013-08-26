<?php

require_once dirname(__FILE__) . '/../Connector/Invoice/Invoice.php';

/**
 * Description of StrategyInvoice
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Model_Builder_StrategyInvoice extends PowerMedia_Ifirma_Model_Builder_StrategyAbstract{
	
	public function makeInvoice() {
		$invoice = new ifirma\Invoice();
		
		$invoice->{ifirma\Invoice::KEY_KONTRAHENT} = $this->createContractorObject();
		
		$invoice->{ifirma\Invoice::KEY_SPOSOB_ZAPLATY} = $this->getPaymentType();
		$invoice->{ifirma\Invoice::KEY_LICZ_OD} = ifirma\Invoice::DEFAULT_VALUE_LICZ_OD;
		$invoice->{ifirma\Invoice::KEY_FORMAT_DATY_SPRZEDAZY} = ifirma\Invoice::DEFAULT_VALUE_FORMAT_DATY_SPRZEDAZY;
		$invoice->{ifirma\Invoice::KEY_DATA_WYSTAWIENIA} = date('Y-m-d');
		$invoice->{ifirma\Invoice::KEY_DATA_SPRZEDAZY} = $this->getOrderDate();
		$invoice->{ifirma\Invoice::KEY_ZAPLACONO} = 0;
		$invoice->{ifirma\Invoice::KEY_RODZAJ_PODPISU_ODBIORCY} = ifirma\Invoice::DEFAULT_VALUE_RODZAJ_PODPISU_ODBIORCY;
		$invoice->{ifirma\Invoice::KEY_UWAGI} = $this->getOrderNumber();
		$invoice->{ifirma\Invoice::KEY_WIDOCZNY_NUMER_GIOS} = ifirma\Invoice::DEFAULT_VALUE_WIDOCZNY_NUMER_GIOS;
		
		foreach($this->getOrder()->getAllVisibleItems() as $item){
			$invoice->addInvoicePosition($this->createInvoicePosition($item));
		}
		
		if($this->isNecessaryToAddShippingPosition()){
			$invoice->addInvoicePosition($this->createInvoicePositionShippingCost());
		}
		
		return $invoice;
	}
}

