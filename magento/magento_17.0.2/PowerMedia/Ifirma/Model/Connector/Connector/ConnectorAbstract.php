<?php

namespace ifirma;

require_once dirname(__FILE__) . '/../IfirmaException.php';
require_once dirname(__FILE__) . '/../DataContainer.php';
require_once dirname(__FILE__) . '/../Config.php';
require_once dirname(__FILE__) . '/../Utils.php';
require_once dirname(__FILE__) . '/../Response.php';
require_once dirname(__FILE__) . '/../Invoice/InvoiceAbstract.php';
require_once dirname(__FILE__) . '/../AccountancyMonth.php';
require_once dirname(__FILE__) . '/../AccountancyMonthParam.php';
require_once dirname(__FILE__) . '/ConnectorInvoice.php';
require_once dirname(__FILE__) . '/ConnectorInvoiceBill.php';
require_once dirname(__FILE__) . '/ConnectorInvoiceProforma.php';
require_once dirname(__FILE__) . '/ConnectorInvoiceSend.php';

/**
 * Description of Connector
 *
 * @author bbojanowicz
 */
abstract class ConnectorAbstract {
	
	const API_INVOICE_URL = 'https://www.ifirma.pl/iapi/fakturakraj.json';
	const API_INVOICE_SEND_URL = 'https://www.ifirma.pl/iapi/fakturawysylka.json';
	const API_ADVANCE_INVOICE_URL = 'https://www.ifirma.pl/iapi/fakturazaliczka.json';
	const API_PROFORMA_URL = 'https://www.ifirma.pl/iapi/fakturaproformakraj.json';
	const API_BILL_URL = 'https://www.ifirma.pl/iapi/rachunekkraj.json';
	const API_ACCOUNTANCY_MONTH_URL = 'https://www.ifirma.pl/iapi/abonent/miesiacksiegowy.json';
	const API_REGISTER_USER_URL = 'https://www.ifirma.pl/iapi/abonent/rejestracja.json';
	
	const API_GET_BILL_URL = 'https://www.ifirma.pl/iapi/rachunekkraj/';
	const API_GET_INVOICE_URL = 'https://www.ifirma.pl/iapi/fakturakraj/';
	const API_GET_INVOICE_SEND_URL = 'https://www.ifirma.pl/iapi/fakturawysylka/';
	const API_GET_INVOICE_PROFORMA_URL = 'https://www.ifirma.pl/iapi/fakturaproformakraj/';
	const API_GET_INVOICE_PROFORMA_ADD_URL = 'https://www.ifirma.pl/iapi/fakturaproformakraj/add/';
	
	const DEFAULT_CONNECTION_FILE_TYPE = 'json';
	const FILE_TYPE_PDF = 'pdf';
	const INVOICE_TYPE_ORIGINAL = 'oryg';
	
	const CURLOPT_TIMEOUT_VALUE = 300;
	const CURLOPT_CONNECTTIMEOUT_VALUE = 100;
	const CURLOPT_RETURNTRANSFER_VALUE = true;
	const CURLOPT_SSL_VERIFYHOST_VALUE = 0;
	const CURLOPT_SSL_VERIFYPEER_VALUE = 0;
	
	/**
	 *
	 * @var InvoiceAbstract
	 */
	protected $_invoice;

	/**
	 *
	 * @var resource
	 */
	protected $_currentCurl;
	
	/**
	 * Ensure that static factory method is the only way to create Connetor instance
	 */
	/*private*/ protected function __construct() {
		;
	}
	
	/**
	 * 
	 * @return \ifirma\ConnectorAbstract
	 * @throws IfirmaException
	 */
	protected function _checkConfiguration(){
		if(!$this->_getConfig()->allDataSet()){
			throw new IfirmaException("Wrong configuration");
		}
		
		return $this;
	}
	
	/**
	 * @return Conf
	 */
	protected function _getConfig(){
		return Config::getInstance();
	}
	
	/**
	 * 
	 * @param \ifirma\InvoiceAbstract $invoice
	 * @return \ifirma\ConnectorAbstract
	 */
	protected function _setInvoice(InvoiceAbstract $invoice){
		$this->_invoice = $invoice;
		
		return $this;
	}
	
	/**
	 * @return ConnectorAbstract
	 * @throws IfirmaException
	 */
	public static function factory(InvoiceAbstract $invoice){
		$connector = self::_getInvoiceConnectorTypeBasedOnInvoice($invoice);
		
		$connector->_checkConfiguration()
				->_setInvoice($invoice);
		
		return $connector;
	}
	
	/**
	 * 
	 * @param \ifirma\InvoiceAbstract $invoice
	 * @throws IfirmaException
	 * @return ConnectorAbstract
	 */
	private static function _getInvoiceConnectorTypeBasedOnInvoice(InvoiceAbstract $invoice){
		require_once dirname(__FILE__) . '/../Invoice/Invoice.php';
		require_once dirname(__FILE__) . '/../Invoice/InvoiceSend.php';
		require_once dirname(__FILE__) . '/../Invoice/InvoiceBill.php';
		require_once dirname(__FILE__) . '/../Invoice/InvoiceProforma.php';
		$invoiceType = get_class($invoice);
		
		switch($invoiceType){
			case Invoice::getType():
			case InvoiceResponse::getType():
				return new ConnectorInvoice();
				break;
			
			case InvoiceBill::getType():
			case InvoiceBillResponse::getType():
				return new ConnectorInvoiceBill();
				break;
			
			case InvoiceProforma::getType():
			case InvoiceProformaResponse::getType():
				return new ConnectorInvoiceProforma();
				break;
			
			case InvoiceSend::getType():
			case InvoiceSendResponse::getType():
				return new ConnectorInvoiceSend();
				break;
			
			default:
				throw new IfirmaException(sprintf("Unsupported invoice type: %s", $invoiceType));
				break;
		}
	}
	
	/**
	 * 
	 * @param string $messageHash
	 * @param string $connectionFileType
	 * @return array
	 */
	protected function _getRequestHeaders($messageHash, $connectionFileType = self::DEFAULT_CONNECTION_FILE_TYPE){
		return array(
			'Accept: application/'.$connectionFileType,
			'Content-type: application/'.$connectionFileType.'; charset=UTF-8',
			'Authentication: IAPIS user='.$this->_getConfig()->{Config::API_LOGIN}.', hmac-sha1='.$messageHash
		);
	}
	
	/**
	 * @return Response
	 */
	public abstract function send();

	/**
	 * @param int $id
	 * @return ConnectorAbstract
	 */
	public abstract function receive($id);
	
	/**
	 * @return binary
	 */
	public abstract function receivePdf();
	
	/**
	 * 
	 * @return void
	 */
	protected function _setProperAccountancyMonth(){
		$currentAccountancyMonth = $this->_getCurrentAccountancyMonth();
		$invoiceAccountancyMonth = $this->_invoice->getAccountancyMonth();
		
		$diffInMonths = AccountancyMonth::diffInMonths($currentAccountancyMonth, $invoiceAccountancyMonth);
		if($diffInMonths === 0){
			return; // no need to change month
		}
		
		$param = (
				$diffInMonths < 0 
				? 
				AccountancyMonthParam::getNextMonthParam()
				: 
				AccountancyMonthParam::getPrevMonthParam()
		);
		
		$diffAbsInMonth = abs($diffInMonths);
		while($diffAbsInMonth--){
			$this->_setChangeAccountancyMonth($param);
		}
		
		// final check
		if(!$invoiceAccountancyMonth->equals($this->_getCurrentAccountancyMonth() /* fresh */)){
			throw new IfirmaException("Unable to set proper accountancy month");
		}
	}
	
	/**
	 * 
	 * @param \ifirma\AccountancyMonthParam $param
	 * @return void
	 */
	protected function _setChangeAccountancyMonth(AccountancyMonthParam $param){
		$jsonParam = $param->toJson();
		$this->_initCurrentCurl(self::API_ACCOUNTANCY_MONTH_URL);
		$key = Utils::hexToStr($this->_getConfig()->{Config::API_KEY_SUBSCRIBER});
		$messageHash = Utils::hmac($key, sprintf(
			"%s%s%s%s",
			self::API_ACCOUNTANCY_MONTH_URL,
			$this->_getConfig()->{Config::API_LOGIN},
			Config::API_KEY_SUBSCRIBBER_NAME,
			$jsonParam
		));
			
		$this->_setCurlPutOptions(
			self::API_ACCOUNTANCY_MONTH_URL,
			$this->_getRequestHeaders($messageHash), 
			$jsonParam
		);
		
		$rsp = curl_exec($this->_currentCurl);
		$response = Response::factory($rsp);
		$this->_checkResponseAfterAccountancyMonthChange($response);
	}
	
	/**
	 * 
	 * @param \ifirma\Response $response
	 * @throws IfirmaException
	 */
	private function _checkResponseAfterAccountancyMonthChange(Response $response){
		if(!$response->isOk()){
			throw new IfirmaException($response->getMessage());
		}
	}
	
	/**
	 * @return AccountancyMonth
	 * @throws IfirmaException
	 */
	protected function _getCurrentAccountancyMonth(){
		$this->_initCurrentCurl(self::API_ACCOUNTANCY_MONTH_URL);
		$key = Utils::hexToStr($this->_getConfig()->{Config::API_KEY_SUBSCRIBER});
		$messageHash = Utils::hmac($key, sprintf(
			"%s%s%s",
			self::API_ACCOUNTANCY_MONTH_URL,
			$this->_getConfig()->{Config::API_LOGIN},
			Config::API_KEY_SUBSCRIBBER_NAME
		));
		
		$this->_setCurlGetOptions(
			self::API_ACCOUNTANCY_MONTH_URL, 
			$this->_getRequestHeaders($messageHash)
		);
		
		$rsp = curl_exec($this->_currentCurl);
		return Response::constructAccountancyMonthFromResponse($rsp);
	}
	
	/**
	 * 
	 * @param string $url
	 * @return \ifirma\ConnectorAbstract
	 * @throws IfirmaException
	 */
	protected function _initCurrentCurl($url){
		$this->_currentCurl = curl_init($url);
		if(!$this->_currentCurl){
			throw new IfirmaException("Unable to init curl");
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $url
	 * @param string $headers
	 * @return \ifirma\ConnectorAbstract
	 */
	protected function _setCurlGetOptions($url, $headers){
		$this->_setCurlOptions($url, $headers);
		
		curl_setopt($this->_currentCurl, CURLOPT_HTTPGET, true);
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $url
	 * @param string $headers
	 * @param string $requestContent
	 * @return \ifirma\ConnectorAbstract
	 */
	protected function _setCurlPutOptions($url, $headers, $requestContent){
		$this->_setCurlOptions($url, $headers);
	
		curl_setopt($this->_currentCurl, CURLOPT_HTTPGET, false);
		curl_setopt($this->_currentCurl, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($this->_currentCurl, CURLOPT_POSTFIELDS, $requestContent);
	
		return $this;
	}
	
	/**
	 * 
	 * @param string $url
	 * @param string $headers
	 * @param string $requestContent
	 * @return \ifirma\ConnectorAbstract
	 */
	protected function _setCurlPostOptions($url, $headers, $requestContent){
		$this->_setCurlOptions($url, $headers);
		
		curl_setopt($this->_currentCurl, CURLOPT_HTTPGET, false);
		curl_setopt($this->_currentCurl, CURLOPT_POST, true);
		curl_setopt($this->_currentCurl, CURLOPT_POSTFIELDS, $requestContent);
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $url
	 * @param array $headers
	 * @return \ifirma\ConnectorAbstract
	 */
	protected function _setCurlOptions($url, $headers){
		curl_setopt($this->_currentCurl, CURLOPT_TIMEOUT, self::CURLOPT_TIMEOUT_VALUE);
		curl_setopt($this->_currentCurl, CURLOPT_CONNECTTIMEOUT, self::CURLOPT_CONNECTTIMEOUT_VALUE);
		curl_setopt($this->_currentCurl, CURLOPT_URL, $url);
		curl_setopt($this->_currentCurl, CURLOPT_RETURNTRANSFER, self::CURLOPT_RETURNTRANSFER_VALUE);
		curl_setopt($this->_currentCurl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($this->_currentCurl, CURLOPT_SSL_VERIFYHOST, self::CURLOPT_SSL_VERIFYHOST_VALUE);
		curl_setopt($this->_currentCurl, CURLOPT_SSL_VERIFYPEER, self::CURLOPT_SSL_VERIFYPEER_VALUE);  
		
		return $this;
	}
	
	/**
	 * 
	 * @param array $data
	 * @throws IfirmaException
	 */
	private function _checkResponseBeforePopulatinginvoice($data){
		if(!isset($data[Response::KEY_RESPONSE_CONTAINER])){
			throw new IfirmaException(sprintf("Unable to create object. Missing \"%s\" container in json response.", Response::KEY_RESPONSE_CONTAINER));
		}
		
		if(isset($data[Response::KEY_RESPONSE_CONTAINER]) 
			&& isset($data[Response::KEY_RESPONSE_CONTAINER][Response::KEY_CODE])
			&& !Response::isResponseCodeOk($data[Response::KEY_RESPONSE_CONTAINER][Response::KEY_CODE])
		){
			throw new IfirmaException(
					isset($data[Response::KEY_RESPONSE_CONTAINER][Response::KEY_INFORMATION])
					?
					$data[Response::KEY_RESPONSE_CONTAINER][Response::KEY_INFORMATION]
					:
					"Invalid response."
			);
		}
	}
	
	/**
	 * 
	 * @param string $rawData
	 * @return \ifirma\ConnectorAbstract
	 * @throws IfirmaException
	 */
	protected function _populateInvoice($rawData){
		$data = json_decode($rawData, true);
		
		$this->_checkResponseBeforePopulatinginvoice($data);
		
		foreach ($this->_invoice->filterKeys(array_keys($data[Response::KEY_RESPONSE_CONTAINER])) as $key){
			switch($key){
				case InvoiceAbstract::KEY_KONTRAHENT:
					$this->_populateFieldContractor($data);
					break;
				case InvoiceAbstract::KEY_POZYCJE:
					$this->_populateFieldEntries($data);
					break;
				default:
					$this->_invoice->$key = $data[Response::KEY_RESPONSE_CONTAINER][$key];
					break;
			}
		}
		
		if(!$this->_invoice->isValid()){
			throw new IfirmaException(
				sprintf(
					"Invalid response document: %s",
					implode("; ", $this->_invoice->getValidationErrorMessages())
					)
			);
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param array $data
	 * @return void
	 */
	private function _populateFieldContractor($data){
		$contractor = new InvoiceContractor();
		
		foreach($contractor->filterKeys(array_keys($data[Response::KEY_RESPONSE_CONTAINER][InvoiceAbstract::KEY_KONTRAHENT])) as $key){
			$contractor->$key = $data[Response::KEY_RESPONSE_CONTAINER][InvoiceAbstract::KEY_KONTRAHENT][$key];
		}
		
		$this->_invoice->{InvoiceAbstract::KEY_KONTRAHENT} = $contractor;
	}
	
	/**
	 * 
	 * @param array $data
	 * @return void
	 */
	private function _populateFieldEntries($data){
		$positions = array();
		
		foreach($data[Response::KEY_RESPONSE_CONTAINER][InvoiceAbstract::KEY_POZYCJE] as $positionData){
			$positions[] = $this->_createPositionEntry($positionData);
		}
		
		$this->_invoice->{InvoiceAbstract::KEY_POZYCJE} = $positions;
	}
	
	/**
	 * 
	 * @param array $positionData
	 * @return InvoicePosition
	 */
	private function _createPositionEntry($positionData){
		$position = $this->_invoice->getEmptyInvoicePositionObject();
		
		foreach($position->filterKeys(array_keys($positionData)) as $positionKey){
			$position->$positionKey = $positionData[$positionKey];
		}
		
		return $position;
	}
}
