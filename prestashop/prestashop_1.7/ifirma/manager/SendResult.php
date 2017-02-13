<?php

namespace ifirma;

/**
 * Description of SendResult
 *
 * @author bbojanowicz
 */
class SendResult {
	
	const VALID = 'valid';
	const INVALID = 'invalid';
	
	const MESSAGE_INVALID_USE = 'Niepoprawne użycie modułu. Brak możliwości obsługi żądania.';
	const MESSAGE_MISSING_INVOICE_PROFORMA = 'Faktura proforma nie istnieje.';
	const MESSAGE_UNABLE_TO_SEND_INVOICE = 'Nie udało się wystawić faktury.';
	const MESSAGE_INVOICE_SUCCESSFULLY_SEND = 'Wystawiono fakturę.';
	const MESSAGE_SEND_OK_UNABLE_TO_SAVE = 'Wystawiono fakturę, ale nie udało się uaktualnić zamówienia w Twoim sklepie.';
	
	/**
	 * 
	 * @param string $res
	 * @param string $message
	 */
	private function __construct($res, $message){
		$this->_result = $res;		//var_dump($sendResult->getMessage()); ;
		$this->_message = $message;
		
	}
	
	/**
	 *
	 * @var string
	 */
	protected $_result;
	
	/**
	 *
	 * @var string
	 */
	protected $_message;
	
	/**
	 * 
	 * @return bool
	 */
	public function isOk(){
		return $this->_result === self::VALID;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getMessage(){
		return $this->_message;
	}
	
	/**
	 * 
	 * @param string $message
	 * @return \self
	 */
	public static function makeValidResponse($message = ''){
		return new self(self::VALID, $message);
	}
	
	/**
	 * 
	 * @param string $message
	 * @return \self
	 */
	
	public static function makeInvalidResponse($message = ''){
		return new self(self::INVALID, $message);
	}
}

