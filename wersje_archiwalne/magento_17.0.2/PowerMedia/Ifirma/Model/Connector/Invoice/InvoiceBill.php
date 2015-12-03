<?php

namespace ifirma;

require_once dirname(__FILE__) . '/InvoiceAbstract.php';
require_once dirname(__FILE__) . '/InvoiceBillPosition.php';
require_once dirname(__FILE__) . '/InvoiceBillResponse.php';

/**
 * Description of Bill
 *
 * @author bbojanowicz
 */
class InvoiceBill extends InvoiceAbstract{
	
	const KEY_WPIS_DO_KPIR = 'WpisDoKpir';
	
	const DEFAULT_VALUE_WPIS_DO_KPIR = 'TOW';
	
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
			self::KEY_ZAPLACONO,
			self::KEY_DATA_WYSTAWIENIA,
			self::KEY_DATA_SPRZEDAZY,
			self::KEY_FORMAT_DATY_SPRZEDAZY,
			self::KEY_SPOSOB_ZAPLATY,
			self::KEY_KONTRAHENT,
			self::KEY_POZYCJE,
			self::KEY_WPIS_DO_KPIR
		);
	}
	
	/**
	 * @return string
	 */
	public function toJson(){
		return json_encode(array(
			self::KEY_KONTRAHENT => $this->{self::KEY_KONTRAHENT}->toArray(),
			self::KEY_POZYCJE => array_map(function(InvoicePosition $position){ return $position->toArray(); }, $this->{self::KEY_POZYCJE}),
			self::KEY_ZAPLACONO => $this->{self::KEY_ZAPLACONO},
			self::KEY_NUMER_KONTA_BANKOWEGO => $this->{self::KEY_NUMER_KONTA_BANKOWEGO},
			self::KEY_DATA_WYSTAWIENIA => $this->{self::KEY_DATA_WYSTAWIENIA},
			self::KEY_MIEJSCE_WYSTAWIENIA => $this->{self::KEY_MIEJSCE_WYSTAWIENIA},
			self::KEY_DATA_SPRZEDAZY => $this->{self::KEY_DATA_SPRZEDAZY},
			self::KEY_FORMAT_DATY_SPRZEDAZY => $this->{self::KEY_FORMAT_DATY_SPRZEDAZY},
			self::KEY_TERMIN_PLATNOSCI => $this->{self::KEY_TERMIN_PLATNOSCI},
			self::KEY_SPOSOB_ZAPLATY => $this->{self::KEY_SPOSOB_ZAPLATY},
			self::KEY_NAZWA_SERII_NUMERACJI => $this->{self::KEY_NAZWA_SERII_NUMERACJI},
			self::KEY_NAZWA_SZABLONU => $this->{self::KEY_NAZWA_SZABLONU},
			self::KEY_PODPIS_ODBIORCY => $this->{self::KEY_PODPIS_ODBIORCY},
			self::KEY_PODPIS_WYSTAWCY => $this->{self::KEY_PODPIS_WYSTAWCY},
			self::KEY_UWAGI => $this->{self::KEY_UWAGI},
			self::KEY_NUMER => $this->{self::KEY_NUMER},
			self::KEY_WPIS_DO_KPIR => $this->{self::KEY_WPIS_DO_KPIR}
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
	 * @return array
	 */
	public function getSupportedKeys(){
		return array(
			self::KEY_KONTRAHENT,
			self::KEY_POZYCJE,
			self::KEY_ZAPLACONO,
			self::KEY_NUMER_KONTA_BANKOWEGO,
			self::KEY_DATA_WYSTAWIENIA,
			self::KEY_MIEJSCE_WYSTAWIENIA,
			self::KEY_DATA_SPRZEDAZY,
			self::KEY_FORMAT_DATY_SPRZEDAZY,
			self::KEY_TERMIN_PLATNOSCI,
			self::KEY_SPOSOB_ZAPLATY,
			self::KEY_NAZWA_SERII_NUMERACJI,
			self::KEY_NAZWA_SZABLONU,
			self::KEY_PODPIS_ODBIORCY,
			self::KEY_PODPIS_WYSTAWCY,
			self::KEY_UWAGI,
			self::KEY_NUMER,
			self::KEY_WPIS_DO_KPIR
		);
	}
	
	/**
	 * 
	 * @param \ifirma\InvoicePosition $position
	 * @return \ifirma\InvoiceAbstract
	 * @throws IfirmaException
	 */
	public function addInvoicePosition(InvoicePosition $position){
		throw new IfirmaException("Unableto add invoice position to bill.");
	}
	
	/**
	 * 
	 * @param \ifirma\InvoiceBillPosition $position
	 * @return type
	 */
	public function addInvoiceBillPosition(InvoiceBillPosition $position){
		return parent::addInvoicePosition($position);
	}

	/**
	 * 
	 * @return \ifirma\InvoiceBillPosition
	 */
	public function getEmptyInvoicePositionObject(){
		return new InvoiceBillPosition();
	}
	
	/**
	 * 
	 * @param int $id
	 * @return InvoiceBill
	 * @throws IfirmaException
	 */
	public static function get($id){
		$bill = new InvoiceBillResponse();
		$bill->disableSendMethod();
		
		ConnectorAbstract::factory($bill)->receive($id);
		
		return $bill;
	}
}

