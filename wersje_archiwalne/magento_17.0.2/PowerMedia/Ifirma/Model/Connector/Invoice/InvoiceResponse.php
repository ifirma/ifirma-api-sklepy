<?php

namespace ifirma;

require_once dirname(__FILE__) . '/Invoice.php';

/**
 * Description of InvoiceResponse
 *
 * @author bbojanowicz
 */
class InvoiceResponse  extends Invoice{
	
	const KEY_ZAPLACONO_NA_DOKUMENCIE = 'ZaplaconoNaDokumencie';
	const KEY_IDENTYFIKATOR_KONTRAHENTA = 'IdentyfikatorKontrahenta';
	const KEY_PREFIKS_UE_KNTRAHENTA = 'PrefiksUEKontrahenta';
	const KEY_NIP_KONTRAHENTA = 'NIPKontrahenta';
	const KEY_PELNY_NUMER = 'PelnyNumer';
	const KEY_WPIS_DO_EWIDENCJI = 'WpisDoEwidencji';
	
	/**
	 * 
	 * @return array
	 */
	public function getSupportedKeys(){
		return array_merge(
			parent::getSupportedKeys(),
			array(
				self::KEY_ZAPLACONO_NA_DOKUMENCIE,
				self::KEY_IDENTYFIKATOR_KONTRAHENTA,
				self::KEY_PREFIKS_UE_KNTRAHENTA,
				self::KEY_NIP_KONTRAHENTA,
				self::KEY_PELNY_NUMER,
				self::KEY_WPIS_DO_EWIDENCJI
			)	
		);
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
			self::KEY_PELNY_NUMER
		);
	}
	
	/**
	 * 
	 * @return string
	 */
	public static function getType(){
		return __CLASS__;
	}
	
	/**
	 * 
	 * @param array $keys
	 * @return array
	 */
	public function filterKeys($keys){
		return self::filterSupportedKeys($this, $keys);	
	}
	
	/**
	 * @return binary
	 */
	public function getPdf(){
		return ConnectorAbstract::factory($this)->receivePdf();
	}
}

