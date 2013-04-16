<?php

namespace ifirma;

require_once dirname(__FILE__) . '/../IfirmaException.php';
require_once dirname(__FILE__) . '/../DataContainer.php';
require_once dirname(__FILE__) . '/../ToJsonInterface.php';
require_once dirname(__FILE__) . '/../ToArrayInterface.php';

/**
 * Description of InvoiceContractor
 *
 * @author bbojanowicz
 */
class InvoiceContractor extends DataContainer implements ToArrayInterface, ToJsonInterface{
	
	const KEY_NAZWA = 'Nazwa';
	const KEY_NAZWA2 = 'Nazwa2';
	const KEY_IDENTYFIKATOR = 'Identyfikator';
	const KEY_PREFIKS_UE = 'PrefiksUE';
	const KEY_NIP = 'NIP';
	const KEY_ULICA = 'Ulica';
	const KEY_KOD_POCZTOWY = 'KodPocztowy';
	const KEY_KRAJ = 'Kraj';
	const KEY_MIEJSCOWOSC = 'Miejscowosc';
	const KEY_EMAIL = 'Email';
	const KEY_TELEFON = 'Telefon';
	const KEY_OSOBA_FIZYCZNA = 'OsobaFizyczna';

	/**
	 * @return array
	 */
	public function getSupportedKeys(){
		return array(
			self::KEY_EMAIL,
			self::KEY_IDENTYFIKATOR,
			self::KEY_KOD_POCZTOWY,
			self::KEY_KRAJ,
			self::KEY_MIEJSCOWOSC,
			self::KEY_NAZWA,
			self::KEY_NAZWA2,
			self::KEY_NIP,
			self::KEY_OSOBA_FIZYCZNA,
			self::KEY_PREFIKS_UE,
			self::KEY_TELEFON,
			self::KEY_ULICA
		);
	}
	
	/**
	 * @return string
	 */
	public function toJson(){
		return json_encode($this->toArray());
	}
	
	/**
	 * 
	 * @return array
	 */
	public function toArray(){
		return array(
			self::KEY_EMAIL => $this->{self::KEY_EMAIL},
			self::KEY_IDENTYFIKATOR => $this->{self::KEY_IDENTYFIKATOR},
			self::KEY_KOD_POCZTOWY => $this->{self::KEY_KOD_POCZTOWY},
			self::KEY_KRAJ => $this->{self::KEY_KRAJ},
			self::KEY_MIEJSCOWOSC => $this->{self::KEY_MIEJSCOWOSC},
			self::KEY_NAZWA => $this->{self::KEY_NAZWA},
			self::KEY_NAZWA2 => $this->{self::KEY_NAZWA2},
			self::KEY_NIP => $this->{self::KEY_NIP},
			self::KEY_OSOBA_FIZYCZNA => $this->{self::KEY_OSOBA_FIZYCZNA},
			self::KEY_PREFIKS_UE => $this->{self::KEY_PREFIKS_UE},
			self::KEY_TELEFON => $this->{self::KEY_TELEFON},
			self::KEY_ULICA => $this->{self::KEY_ULICA} 
		);
	}
	
	/**
	 * 
	 * @param array $keys
	 * @return array
	 */
	public function filterKeys($keys){
		return self::filterSupportedKeys($this, $keys);	
	}
}

