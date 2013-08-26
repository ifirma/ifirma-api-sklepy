<?php

require_once dirname(__FILE__) . '/../Connector/Invoice/InvoiceProforma.php';

/**
 * Description of StrategyProforma
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Model_Builder_StrategyProforma extends PowerMedia_Ifirma_Model_Builder_StrategyAbstract{
	
	public function makeInvoice() {
		$invoiceProforma = new ifirma\InvoiceProforma();
		
		$invoiceProforma->{ifirma\InvoiceProforma::KEY_KONTRAHENT} = $this->createContractorObject();
		
		$invoiceProforma->{ifirma\InvoiceProforma::KEY_SPOSOB_ZAPLATY} = $this->getPaymentType();
		$invoiceProforma->{ifirma\InvoiceProforma::KEY_LICZ_OD} = ifirma\InvoiceProforma::DEFAULT_VALUE_LICZ_OD;
		$invoiceProforma->{ifirma\InvoiceProforma::KEY_DATA_WYSTAWIENIA} = date('Y-m-d');
		$invoiceProforma->{ifirma\InvoiceProforma::KEY_RODZAJ_PODPISU_ODBIORCY} = ifirma\InvoiceProforma::DEFAULT_VALUE_RODZAJ_PODPISU_ODBIORCY;
		$invoiceProforma->{ifirma\InvoiceProforma::KEY_NUMER_ZAMOWIENIA} = $this->getRawOrderNumber();
		$invoiceProforma->{ifirma\InvoiceProforma::KEY_WIDOCZNY_NUMER_GIOS} = ifirma\InvoiceProforma::DEFAULT_VALUE_WIDOCZNY_NUMER_GIOS;
		$invoiceProforma->{ifirma\InvoiceProforma::KEY_TYP_FAKTURY_KRAJOWEJ} = ifirma\InvoiceProforma::DEFAULT_VALUE_TYP_FAKTURY_KRAJOWEJ;
		
		foreach($this->getOrder()->getAllVisibleItems() as $item){
			$invoiceProforma->addInvoicePosition($this->createInvoicePosition($item));
		}
		
		if($this->isNecessaryToAddShippingPosition()){
			$invoiceProforma->addInvoicePosition($this->createInvoicePositionShippingCost());
		}
		
		return $invoiceProforma;
	}
}

