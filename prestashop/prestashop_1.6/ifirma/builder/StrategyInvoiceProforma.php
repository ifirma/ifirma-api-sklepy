<?php

namespace ifirma;

require_once dirname(__FILE__) . '/StrategyAbstract.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceProforma.php';

/**
 * Description of StrategyInvoiceProforma
 *
 * @author bbojanowicz
 */
class StrategyInvoiceProforma extends StrategyAbstract{
	
	/**
	 * 
	 * @return \ifirma\InvoiceProforma
	 */
	public function makeInvoice() {
		$invoice = new InvoiceProforma();
		
		$invoice->{InvoiceProforma::KEY_KONTRAHENT} = $this->_createContractorObject();
		
		$invoice->{InvoiceProforma::KEY_SPOSOB_ZAPLATY} = $this->_getPaymentType();
		$invoice->{InvoiceProforma::KEY_LICZ_OD} = InvoiceProforma::DEFAULT_VALUE_LICZ_OD;
		$invoice->{InvoiceProforma::KEY_DATA_WYSTAWIENIA} = date('Y-m-d');
		$invoice->{InvoiceProforma::KEY_RODZAJ_PODPISU_ODBIORCY} = InvoiceProforma::DEFAULT_VALUE_RODZAJ_PODPISU_ODBIORCY;
		$invoice->{InvoiceProforma::KEY_NUMER_ZAMOWIENIA} = $this->_getRawOrderNumber();
		$invoice->{InvoiceProforma::KEY_WIDOCZNY_NUMER_GIOS} = InvoiceProforma::DEFAULT_VALUE_WIDOCZNY_NUMER_GIOS;
		$invoice->{InvoiceProforma::KEY_TYP_FAKTURY_KRAJOWEJ} = InvoiceProforma::DEFAULT_VALUE_TYP_FAKTURY_KRAJOWEJ;
		$invoice->{InvoiceProforma::KEY_MIEJSCE_WYSTAWIENIA} = $this->_getConfig()->{Config::API_MIEJSCE_WYSTAWIENIA};
		$invoice->{InvoiceProforma::KEY_NAZWA_SERII_NUMERACJI} = $this->_getConfig()->{Config::API_NAZWA_SERII_NUMERACJI};
		
		foreach($this->_order->getProducts() as $product){
			$invoice->addInvoicePosition($this->_createInvoicePosition($product));
		}
		
		if($this->_isNecessaryToAddShippingPosition()){
			$invoice->addInvoicePosition($this->_createInvoicePositionShippingCost());
		}
		
		return $invoice;
	}
}

