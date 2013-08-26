<?php

namespace ifirma;

require_once dirname(__FILE__) . '/InvoiceAbstract.php';
require_once dirname(__FILE__) . '/InvoiceProformaResponse.php';

/**
 * Description of InvoiceProforma
 *
 * @author bbojanowicz
 */
class InvoiceProforma extends InvoiceAbstract{
	
	const KEY_TYP_FAKTURY_KRAJOWEJ = 'TypFakturyKrajowej';
	const KEY_NUMER_ZAMOWIENIA = 'NumerZamowienia';
	
	const DEFAULT_VALUE_TYP_FAKTURY_KRAJOWEJ = 'SPRZ';
	
	/**
	 * 
	 * @return string
	 */
	public static function getType(){
		return __CLASS__;
	}
	
	/**
	 * 
	 * @return array
	 */
	protected function _getRequiredFields(){
		return array(
			self::KEY_LICZ_OD,
			self::KEY_DATA_WYSTAWIENIA,
			self::KEY_SPOSOB_ZAPLATY,
			self::KEY_RODZAJ_PODPISU_ODBIORCY,
			self::KEY_WIDOCZNY_NUMER_GIOS,
			self::KEY_KONTRAHENT,
			self::KEY_POZYCJE,
			self::KEY_TYP_FAKTURY_KRAJOWEJ
		);
	}
	
	/**
	 * 
	 * @return string
	 */
	public function toJson(){
		return json_encode(array(
			self::KEY_KONTRAHENT => $this->{self::KEY_KONTRAHENT}->toArray(),
			self::KEY_POZYCJE => array_map(function(InvoicePosition $position){ return $position->toArray(); }, $this->{self::KEY_POZYCJE}),
			self::KEY_LICZ_OD => $this->{self::KEY_LICZ_OD},
			self::KEY_NUMER_KONTA_BANKOWEGO => $this->{self::KEY_NUMER_KONTA_BANKOWEGO},
			self::KEY_DATA_WYSTAWIENIA => $this->{self::KEY_DATA_WYSTAWIENIA},
			self::KEY_MIEJSCE_WYSTAWIENIA => $this->{self::KEY_MIEJSCE_WYSTAWIENIA},
			self::KEY_TERMIN_PLATNOSCI => $this->{self::KEY_TERMIN_PLATNOSCI},
			self::KEY_SPOSOB_ZAPLATY => $this->{self::KEY_SPOSOB_ZAPLATY},
			self::KEY_NAZWA_SZABLONU => $this->{self::KEY_NAZWA_SZABLONU},
			self::KEY_RODZAJ_PODPISU_ODBIORCY => $this->{self::KEY_RODZAJ_PODPISU_ODBIORCY},
			self::KEY_PODPIS_ODBIORCY => $this->{self::KEY_PODPIS_ODBIORCY},
			self::KEY_PODPIS_WYSTAWCY => $this->{self::KEY_PODPIS_WYSTAWCY},
			self::KEY_UWAGI => $this->{self::KEY_UWAGI},
			self::KEY_WIDOCZNY_NUMER_GIOS => $this->{self::KEY_WIDOCZNY_NUMER_GIOS},
			self::KEY_NUMER => $this->{self::KEY_NUMER},
			self::KEY_NUMER_ZAMOWIENIA => $this->{self::KEY_NUMER_ZAMOWIENIA},
			self::KEY_TYP_FAKTURY_KRAJOWEJ => $this->{self::KEY_TYP_FAKTURY_KRAJOWEJ}				
		));
	}
	
	/**
	 * @return bool
	 */
	public function isValid(){
		$this->resetErrors();
		
		return $this->_checkPresenceOfRequiredFields() 
				&&
				$this->_checkInvoiceDatesFormat()
				;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getSupportedKeys(){
		return array(
			self::KEY_KONTRAHENT,
			self::KEY_POZYCJE,
			self::KEY_LICZ_OD,
			self::KEY_NUMER_KONTA_BANKOWEGO,
			self::KEY_DATA_WYSTAWIENIA,
			self::KEY_MIEJSCE_WYSTAWIENIA,
			self::KEY_TERMIN_PLATNOSCI,
			self::KEY_SPOSOB_ZAPLATY,
			self::KEY_NAZWA_SZABLONU,
			self::KEY_RODZAJ_PODPISU_ODBIORCY,
			self::KEY_PODPIS_ODBIORCY,
			self::KEY_PODPIS_WYSTAWCY,
			self::KEY_UWAGI,
			self::KEY_WIDOCZNY_NUMER_GIOS,
			self::KEY_NUMER,
			self::KEY_TYP_FAKTURY_KRAJOWEJ,
			self::KEY_NUMER_ZAMOWIENIA
		);
	}
	
	/**
	 * 
	 * @return array
	 */
	protected function _getDatesFields(){
		return array(
			self::KEY_DATA_WYSTAWIENIA
		);
	}
	
	/**
	 * 
	 * @param int $id
	 * @return InvoiceProformaResponse
	 * @throws IfirmaException
	 */
	public static function get($id){
		$invoice = new InvoiceProformaResponse();
		$invoice->disableSendMethod();
		
		ConnectorAbstract::factory($invoice)->receive($id);
		
		return $invoice;
	}
}

