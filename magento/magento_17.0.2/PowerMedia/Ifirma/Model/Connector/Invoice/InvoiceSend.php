<?php

namespace ifirma;

require_once dirname(__FILE__) . '/InvoiceAbstract.php';
require_once dirname(__FILE__) . '/InvoiceSendResponse.php';

/**
 * Description of InvoiceSend
 *
 * @author bbojanowicz
 */
class InvoiceSend extends InvoiceAbstract{
	
	const KEY_DATA_OTRZYMANIA_ZAPLATY = 'DataOtrzymaniaZaplaty';
	
	/**
	 * 
	 * @return string
	 */
	public static function getType(){
		return __CLASS__;
	}
	
	/**
	 * @return array
	 */
	public function getSupportedKeys(){
		return array(
			self::KEY_KONTRAHENT,
			self::KEY_POZYCJE,
			self::KEY_ZAPLACONO,
			self::KEY_LICZ_OD,
			self::KEY_NUMER_KONTA_BANKOWEGO,
			self::KEY_DATA_WYSTAWIENIA,
			self::KEY_MIEJSCE_WYSTAWIENIA,
			self::KEY_DATA_SPRZEDAZY,
			self::KEY_FORMAT_DATY_SPRZEDAZY,
			self::KEY_TERMIN_PLATNOSCI,
			self::KEY_NAZWA_SERII_NUMERACJI,
			self::KEY_NAZWA_SZABLONU,
			self::KEY_RODZAJ_PODPISU_ODBIORCY,
			self::KEY_PODPIS_ODBIORCY,
			self::KEY_PODPIS_WYSTAWCY,
			self::KEY_UWAGI,
			self::KEY_WIDOCZNY_NUMER_GIOS,
			self::KEY_NUMER,
			self::KEY_DATA_OTRZYMANIA_ZAPLATY
		);
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
	 * @return string
	 */
	public function toJson(){
		return json_encode(array(
			self::KEY_KONTRAHENT => $this->{self::KEY_KONTRAHENT}->toArray(),
			self::KEY_POZYCJE => array_map(function(InvoicePosition $position){ return $position->toArray(); }, $this->{self::KEY_POZYCJE}),
			self::KEY_ZAPLACONO => $this->{self::KEY_ZAPLACONO},
			self::KEY_LICZ_OD => $this->{self::KEY_LICZ_OD},
			self::KEY_NUMER_KONTA_BANKOWEGO => $this->{self::KEY_NUMER_KONTA_BANKOWEGO},
			self::KEY_DATA_WYSTAWIENIA => $this->{self::KEY_DATA_WYSTAWIENIA},
			self::KEY_MIEJSCE_WYSTAWIENIA => $this->{self::KEY_MIEJSCE_WYSTAWIENIA},
			self::KEY_DATA_SPRZEDAZY => $this->{self::KEY_DATA_SPRZEDAZY},
			self::KEY_FORMAT_DATY_SPRZEDAZY => $this->{self::KEY_FORMAT_DATY_SPRZEDAZY},
			self::KEY_TERMIN_PLATNOSCI => $this->{self::KEY_TERMIN_PLATNOSCI},
			self::KEY_NAZWA_SERII_NUMERACJI => $this->{self::KEY_NAZWA_SERII_NUMERACJI},
			self::KEY_NAZWA_SZABLONU => $this->{self::KEY_NAZWA_SZABLONU},
			self::KEY_RODZAJ_PODPISU_ODBIORCY => $this->{self::KEY_RODZAJ_PODPISU_ODBIORCY},
			self::KEY_PODPIS_ODBIORCY => $this->{self::KEY_PODPIS_ODBIORCY},
			self::KEY_PODPIS_WYSTAWCY => $this->{self::KEY_PODPIS_WYSTAWCY},
			self::KEY_UWAGI => $this->{self::KEY_UWAGI},
			self::KEY_WIDOCZNY_NUMER_GIOS => $this->{self::KEY_WIDOCZNY_NUMER_GIOS},
			self::KEY_NUMER => $this->{self::KEY_NUMER},
			self::KEY_DATA_OTRZYMANIA_ZAPLATY => $this->{self::KEY_DATA_OTRZYMANIA_ZAPLATY}
		));
	}
	
	/**
	 * 
	 * @return array
	 */
	protected function _getRequiredFields(){
		return array(
			self::KEY_ZAPLACONO,
			self::KEY_LICZ_OD,
			self::KEY_DATA_WYSTAWIENIA,
			self::KEY_DATA_SPRZEDAZY,
			self::KEY_FORMAT_DATY_SPRZEDAZY,
			self::KEY_RODZAJ_PODPISU_ODBIORCY,
			self::KEY_WIDOCZNY_NUMER_GIOS,
			self::KEY_KONTRAHENT,
			self::KEY_POZYCJE
		);
	}
	
	/**
	 * 
	 * @param int $id
	 * @return InvoiceSendResponse
	 * @throws IfirmaException
	 */
	public static function get($id){
		$invoice = new InvoiceSendResponse();
		$invoice->disableSendMethod();
		
		ConnectorAbstract::factory($invoice)->receive($id);
		
		return $invoice;
	}
}

