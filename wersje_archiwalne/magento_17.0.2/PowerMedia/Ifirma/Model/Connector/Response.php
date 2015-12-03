<?php

namespace ifirma;

require_once dirname(__FILE__) . '/IfirmaException.php';
require_once dirname(__FILE__) . '/AccountancyMonth.php';

/**
 * Description of Response
 *
 * @author bbojanowicz
 */
class Response {
	
	const OK_RESPONSE_CODE = 0;
	const OK_RESPONSE_CODE2 = 200;
	const INVALID_DATA_CODE = 500;
	
	const KEY_RESPONSE_ACCOUNTANCY_MONTH = 'response';
	const KEY_RESPONSE_ACCOUNTANCY_MONTH_MONTH = 'MiesiacKsiegowy';
	const KEY_RESPONSE_ACCOUNTANCY_MONTH_YEAR = 'RokKsiegowy';
	
	const KEY_RESPONSE_CONTAINER = 'response';
	const KEY_CODE = 'Kod';
	const KEY_INFORMATION = 'Informacja';
	const KEY_INVOICE_ID = 'Identyfikator';
	
	/**
	 *
	 * @var int
	 */
	private $_responseCode;
	
	/**
	 *
	 * @var string
	 */
	private $_message;
	
	/**
	 *
	 * @var array
	 */
	private $_content;
	
	/**
	 * Enforce using static factory method
	 */
	private function __construct() {
		// do nothig
	}

	/**
	 * @return string
	 */
	public function getMessage(){
		return (
			$this->_message === null
			?
			''
			:
			$this->_message
			);
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isOk(){
		return self::isResponseCodeOk($this->_responseCode);
	}
	
	/**
	 * 
	 * @param int|string $responseCode
	 * @return bool
	 */
	public static function isResponseCodeOk($responseCode){
		return $responseCode == self::OK_RESPONSE_CODE
			||
			$responseCode == self::OK_RESPONSE_CODE2;
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getContent(){
		return $this->_content;
	}
	
	/**
	 * 
	 * @param type $data
	 * @return \self
	 */
	public static function factory($data){
		$response = new self();
		
		$decodedData = json_decode($data, true);
		if(
			!isset($decodedData[self::KEY_RESPONSE_CONTAINER])
			||
			!isset($decodedData[self::KEY_RESPONSE_CONTAINER][self::KEY_CODE])
		){
			$response->_responseCode = self::INVALID_DATA_CODE;
			$response->_message = "Invalid response format";
		} else {
			$response->_responseCode = $decodedData[self::KEY_RESPONSE_CONTAINER][self::KEY_CODE];
			$response->_message = (
					isset($decodedData[self::KEY_RESPONSE_CONTAINER][self::KEY_INFORMATION])
					?
					$decodedData[self::KEY_RESPONSE_CONTAINER][self::KEY_INFORMATION]
					:
					''
			);
			$response->_content = self::_constructResponseContent($decodedData);
		}
		
		return $response;
	}
	
	/**
	 * 
	 * @param array $data
	 * @return array
	 */
	private static function _constructResponseContent($data){
		$content = array();
		
		if(isset($data[self::KEY_RESPONSE_CONTAINER][self::KEY_INVOICE_ID])){
			$content[self::KEY_INVOICE_ID] = $data[self::KEY_RESPONSE_CONTAINER][self::KEY_INVOICE_ID];
		}
		
		return $content;
	}
	
	/**
	 * 
	 * @param string $response
	 * @return AccountancyMonth
	 * @throws IfirmaException
	 */
	public static function constructAccountancyMonthFromResponse($response){
		$responseData = json_decode($response, true);
		
		if(
			!isset($responseData[self::KEY_RESPONSE_ACCOUNTANCY_MONTH])
			||
			!isset($responseData[self::KEY_RESPONSE_ACCOUNTANCY_MONTH][self::KEY_RESPONSE_ACCOUNTANCY_MONTH_MONTH])
			||
			!isset($responseData[self::KEY_RESPONSE_ACCOUNTANCY_MONTH][self::KEY_RESPONSE_ACCOUNTANCY_MONTH_YEAR])
			||
			!is_numeric($responseData[self::KEY_RESPONSE_ACCOUNTANCY_MONTH][self::KEY_RESPONSE_ACCOUNTANCY_MONTH_MONTH])
			||
			!is_numeric($responseData[self::KEY_RESPONSE_ACCOUNTANCY_MONTH][self::KEY_RESPONSE_ACCOUNTANCY_MONTH_YEAR])
		){
			throw new IfirmaException("Invalid response while geting assountancy month");
		}
		
		return new AccountancyMonth(
			intval($responseData[self::KEY_RESPONSE_ACCOUNTANCY_MONTH][self::KEY_RESPONSE_ACCOUNTANCY_MONTH_MONTH]),
			intval($responseData[self::KEY_RESPONSE_ACCOUNTANCY_MONTH][self::KEY_RESPONSE_ACCOUNTANCY_MONTH_YEAR])
		);
	}
}

