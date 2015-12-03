<?php

namespace ifirma;

require_once dirname(__FILE__) . '/../ToJsonInterface.php';
require_once dirname(__FILE__) . '/../Connector/ConnectorAbstract.php';
require_once dirname(__FILE__) . '/InvoicePosition.php';
require_once dirname(__FILE__) . '/InvoiceContractor.php';
require_once dirname(__FILE__) . '/../Filters/InvoiceNumberFilter.php';

/**
 * Description of InvoiceAbstract
 *
 * @author bbojanowicz
 */
abstract class InvoiceAbstract extends DataContainer implements ToJsonInterface{
		
	const KEY_KONTRAHENT = 'Kontrahent';
	const KEY_POZYCJE = 'Pozycje';
	const KEY_ZAPLACONO = 'Zaplacono';
	const KEY_LICZ_OD = 'LiczOd';
	const KEY_NUMER_KONTA_BANKOWEGO = 'NumerKontaBankowego';
	const KEY_DATA_WYSTAWIENIA = 'DataWystawienia';
	const KEY_MIEJSCE_WYSTAWIENIA = 'MiejsceWystawienia';
	const KEY_DATA_SPRZEDAZY = 'DataSprzedazy';
	const KEY_FORMAT_DATY_SPRZEDAZY = 'FormatDatySprzedazy';
	const KEY_TERMIN_PLATNOSCI = 'TerminPlatnosci';
	const KEY_SPOSOB_ZAPLATY = 'SposobZaplaty';
	const KEY_NAZWA_SERII_NUMERACJI = 'NazwaSeriiNumeracji';
	const KEY_NAZWA_SZABLONU = 'NazwaSzablonu';
	const KEY_RODZAJ_PODPISU_ODBIORCY = 'RodzajPodpisuOdbiorcy';
	const KEY_PODPIS_ODBIORCY = 'PodpisOdbiorcy';
	const KEY_PODPIS_WYSTAWCY = 'PodpisWystawcy';
	const KEY_UWAGI = 'Uwagi';
	const KEY_WIDOCZNY_NUMER_GIOS = 'WidocznyNumerGios';
	const KEY_NUMER = 'Numer';
	
	const VALID_DATE_FORMAT = '/^\d{4}-\d{2}-\d{2}$/'; // RRRR-MM-DD
	
	const DEFAULT_VALUE_RODZAJ_PODPISU_ODBIORCY = 'BPO';
	const DEFAULT_VALUE_FORMAT_DATY_SPRZEDAZY = 'DZN';
	const DEFAULT_VALUE_LICZ_OD = 'BRT';
	const DEFAULT_VALUE_SPOSOB_ZAPLATY = 'PRZ';
	const DEFAULT_VALUE_WIDOCZNY_NUMER_GIOS = false;
	
	/**
	 *
	 * @var bool
	 */
	protected $_disableSend = false;
	
	/**
	 * 
	 * @return \ifirma\InvoiceAbstract
	 */
	public function disableSendMethod(){
		$this->_disableSend = true;
		
		return $this;
	}
	
	/**
	 *
	 * @var array
	 */
	protected $_validationErrorMessages = array();

	/**
	 * 
	 * @return array
	 */
	public function getValidationErrorMessages(){
		return $this->_validationErrorMessages;
	}
	
	/**
	 * @return string
	 */
	public function getFirstValidationErrorMessage(){
		return (
			count($this->_validationErrorMessages) === 0
			?
			''
			:
			$this->_validationErrorMessages[0]
		);
	}
	
	/**
	 * 
	 * @return \ifirma\InvoiceAbstract
	 */
	public function resetErrors(){
		$this->_validationErrorMessages = array();
		
		return $this;
	}
	
	/**
	 * 
	 * @return \ifirma\Response
	 */
	public function send(){
		if($this->_disableSend){
			throw new IfirmaException("Unable to send this request");
		}
		
		return ConnectorAbstract::factory($this)->send();
	}
	
	/**
	 * @return AccountancyMonth
	 */
	public function getAccountancyMonth(){
		$dateParts = explode('-', $this->{self::KEY_DATA_WYSTAWIENIA});
		
		if(count($dateParts) !== 3){
			throw new IfirmaException(sprintf("Zły format danych dla pola \"%s\".", self::KEY_DATA_WYSTAWIENIA));
		}
		
		return new AccountancyMonth($dateParts[1], $dateParts[0]);
	}
	
	/**
	 * @return bool
	 */
	abstract public function isValid();
	
	/**
	 * @return array
	 */
	abstract protected function _getRequiredFields();
	
	/**
	 * 
	 * @return boolean
	 */
	protected function _checkPresenceOfRequiredFields(){
		$result = true;
		
		foreach($this->_getRequiredFields() as $requiredFieldName){
			if(!isset($this->_values[$requiredFieldName])){
				$this->_validationErrorMessages[] = sprintf(
						"Brak wymaganego pola \"%s\".",
						$requiredFieldName
						);
				$result = false;
			}
		}
		
		return $result;
	}
	
	/**
	 * @retun boolean
	 */
	protected function _checkInvoiceDatesFormat(){
		$result = true;
		
		foreach($this->_getDatesFields() as $dateFieldName){
			if(!preg_match(self::VALID_DATE_FORMAT, ($this->{$dateFieldName} !== null ? $this->{$dateFieldName} : ''))){
				$this->_validationErrorMessages[] = sprintf(
					"Nieprawidłowy format daty dla pola \"%s\".",
					$dateFieldName
				);
				$result = false;
				continue;
			}
			
			$dateParts = explode('-', $this->{$dateFieldName});
			if(!checkdate($dateParts[1], $dateParts[2], $dateParts[0])){
				$this->_validationErrorMessages[] = sprintf(
					"Nieprawidłowa data dla pola \"%s\".",
					$dateFieldName
				);
				$result = false;
			}
		}
		
		return $result;
	}
	
	/**
	 * 
	 * @return array
	 */
	protected function _getDatesFields(){
		return array(
			self::KEY_DATA_SPRZEDAZY,
			self::KEY_DATA_WYSTAWIENIA
		);
	}
	
	/**
	 * 
	 * @param \ifirma\InvoicePosition $position
	 * @return \ifirma\InvoiceAbstract
	 */
	public function addInvoicePosition(InvoicePosition $position){
		if($this->{self::KEY_POZYCJE} === null){
			$this->{self::KEY_POZYCJE} = array($position);
		} else {
			$this->{self::KEY_POZYCJE} = array_merge($this->{self::KEY_POZYCJE}, array($position));
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @return \ifirma\InvoicePosition
	 */
	public function getEmptyInvoicePositionObject(){
		return new InvoicePosition();
	}
	
	/**
	 * 
	 * @param string $number
	 * @return string
	 */
	public static function filterNumber($number){
		$filter = new InvoiceNumberFilter();
		
		return $filter->filter($number);
	}
}

