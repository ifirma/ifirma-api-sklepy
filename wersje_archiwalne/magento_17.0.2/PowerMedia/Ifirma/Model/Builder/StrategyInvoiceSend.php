<?php

require_once dirname(__FILE__) . '/../Connector/Invoice/InvoiceSend.php';

/**
 * Description of StrategyInvoiceSend
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Model_Builder_StrategyInvoiceSend extends PowerMedia_Ifirma_Model_Builder_StrategyAbstract{
	
	public function makeInvoice() {
		$invoiceSend = new ifirma\InvoiceSend();
		
		$invoiceSend->{ifirma\InvoiceSend::KEY_KONTRAHENT} = $this->createContractorObject();
		
		$invoiceSend->{ifirma\InvoiceSend::KEY_LICZ_OD} = ifirma\InvoiceSend::DEFAULT_VALUE_LICZ_OD;
		$invoiceSend->{ifirma\InvoiceSend::KEY_FORMAT_DATY_SPRZEDAZY} = ifirma\InvoiceSend::DEFAULT_VALUE_FORMAT_DATY_SPRZEDAZY;
		$invoiceSend->{ifirma\InvoiceSend::KEY_DATA_WYSTAWIENIA} = date('Y-m-d');
		$invoiceSend->{ifirma\InvoiceSend::KEY_DATA_SPRZEDAZY} = $this->getOrderDate();
		$invoiceSend->{ifirma\InvoiceSend::KEY_ZAPLACONO} = 0;
		$invoiceSend->{ifirma\InvoiceSend::KEY_RODZAJ_PODPISU_ODBIORCY} = ifirma\InvoiceSend::DEFAULT_VALUE_RODZAJ_PODPISU_ODBIORCY;
		$invoiceSend->{ifirma\InvoiceSend::KEY_UWAGI} = $this->getOrderNumber();
		$invoiceSend->{ifirma\InvoiceSend::KEY_WIDOCZNY_NUMER_GIOS} = ifirma\InvoiceSend::DEFAULT_VALUE_WIDOCZNY_NUMER_GIOS;
		
		foreach($this->getOrder()->getAllVisibleItems() as $item){
			$invoiceSend->addInvoicePosition($this->createInvoicePosition($item));
		}
		
		if($this->isNecessaryToAddShippingPosition()){
			$invoiceSend->addInvoicePosition($this->createInvoicePositionShippingCost());
		}
		
		return $invoiceSend;
	}
}

