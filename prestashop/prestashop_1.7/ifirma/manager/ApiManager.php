<?php

namespace ifirma;

require_once dirname(__FILE__) . '/../models/InvoiceMap.php';
require_once dirname(__FILE__) . '/../ifirma.php';
require_once dirname(__FILE__) . '/../connector/GetPdfInterface.php';
require_once dirname(__FILE__) . '/../connector/Config.php';
require_once dirname(__FILE__) . '/../connector/Invoice/Invoice.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceBill.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceSend.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceProforma.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceBillResponse.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceProformaResponse.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceResponse.php';
require_once dirname(__FILE__) . '/../connector/Invoice/InvoiceSendResponse.php';
require_once dirname(__FILE__) . '/SendResult.php';
require_once dirname(__FILE__) . '/../builder/InvoiceBuilder.php';

/**
 * Description of ApiManager
 *
 * @author bbojanowicz
 */
class ApiManager {
	
	const KEY_HASH_NAME = 'h';
	const KEY_ACTION_INVOICE = 'inv';
	const KEY_ACTION_INVOICE_SEND = 'invsend';
	const KEY_ACTION_INVOICE_PROFORMA = 'invpro';
	const KEY_ACTION_INVOICE_FROM_PROFORMA = 'invfinvpro';
	const KEY_ACTION_BILL = 'bill';
	
	const UNEXISTING_PDF_NAME = 'nieistnieje.pdf';
	const PDF_NAME_PREFIX_INVOICE = 'Faktura';
	const PDF_NAME_PREFIX_BILL = 'Rachunek';
	
	/**
	 *
	 * @var ApiManager
	 */
	private static $_instance;
	
	private function __construct() {
		;
	}
	
	/**
	 * 
	 * @return ApiManager
	 */
	public static function getInstance(){
		if(self::$_instance === null){
			self::$_instance = new ApiManager();
		}
		
		return self::$_instance;
	}
		
	/**
	 * @return bool
	 */
	public function checkIfirmaHash(){
		$hash = \Tools::getValue(self::KEY_HASH_NAME);
		return ($hash == \Configuration::get(\Ifirma::API_HASH));
	}
	
	/**
	 * 
	 * @param int $invoiceMapId
	 * @return string|null
	 */
	public function getDocumentAsPdf($invoiceMapId){
		$this->setConfiguration();
		$invoiceMap = InvoiceMap::get($invoiceMapId);

		if($invoiceMap === null){
			return null;
		}
		
		$invoiceResponseObj = $this->_getInvoiceResponseObject($invoiceMap);
		if($invoiceResponseObj === null){
			return null;
		}
		
		return $invoiceResponseObj->getPdf($invoiceMap->{Invoicemap::COLUMN_NAME_INVOICE_ID});
	}
	
	/**
	 * 
	 * @param \ifirma\InvoiceMap $invoiceMap
	 * @return \ifirma\GetPdfInterface
	 */
	private function _getInvoiceResponseObject(InvoiceMap $invoiceMap){
		switch($invoiceMap->{InvoiceMap::COLUMN_NAME_INVOICE_TYPE}){
			case InvoiceMap::INVOICE_TYPE_BILL:
				return InvoiceBill::get($invoiceMap->{InvoiceMap::COLUMN_NAME_INVOICE_ID});
				break;
			
			case InvoiceMap::INVOICE_TYPE_NORMAL:
				return Invoice::get($invoiceMap->{InvoiceMap::COLUMN_NAME_INVOICE_ID});
				break;
			
			case InvoiceMap::INVOICE_TYPE_PROFORMA:
				return InvoiceProforma::get($invoiceMap->{InvoiceMap::COLUMN_NAME_INVOICE_ID});
				break;
			
			case InvoiceMap::INVOICE_TYPE_SEND:
				return InvoiceSend::get($invoiceMap->{InvoiceMap::COLUMN_NAME_INVOICE_ID});
				break;
		}
	}
	
	/**
	 * 
	 * @param int $invoiceMapId
	 * @return string
	 */
	public function getDocumentPdfName($invoiceMapId){
		$invoiceMap = InvoiceMap::get($invoiceMapId);
		
		if($invoiceMap === null){
			return self::UNEXISTING_PDF_NAME;
		}
		
		return sprintf(
			"%s_%s.pdf",
			(
				$invoiceMap->{InvoiceMap::COLUMN_NAME_INVOICE_TYPE} === InvoiceMap::INVOICE_TYPE_BILL
				?
				self::PDF_NAME_PREFIX_BILL		
				:
				self::PDF_NAME_PREFIX_INVOICE
			),
			$invoiceMap->getFilteredNumber()
			);
	}
	
	/**
	 * 
	 * @return \ifirma\ApiManager
	 */
	public function setConfiguration(){
		Config::getInstance()->{Config::API_VAT} = \Configuration::get(\Ifirma::API_VAT);
		Config::getInstance()->{Config::API_LOGIN} = \Configuration::get(\Ifirma::API_LOGIN);
		Config::getInstance()->{Config::API_KEY_BILL} = \Configuration::get(\Ifirma::API_KEY_BILL);
		Config::getInstance()->{Config::API_KEY_INVOICE} = \Configuration::get(\Ifirma::API_KEY_INVOICE);
		Config::getInstance()->{Config::API_KEY_SUBSCRIBER} = \Configuration::get(\Ifirma::API_KEY_SUBSCRIBER);
		Config::getInstance()->{Config::API_RYCZALT} = \Configuration::get(\Ifirma::API_RYCZALT);
		Config::getInstance()->{Config::API_RYCZALT_RATE} = \Configuration::get(\Ifirma::API_RYCZALT_RATE);
		Config::getInstance()->{Config::API_RYCZALT_WPIS_DO_EWIDENCJI} = \Configuration::get(\Ifirma::API_RYCZALT_WPIS_DO_EWIDENCJI);
		Config::getInstance()->{Config::API_PODSTAWA_PRAWNA} = \Configuration::get(\Ifirma::API_PODSTAWA_PRAWNA);
		Config::getInstance()->{Config::API_MIEJSCE_WYSTAWIENIA} = \Configuration::get(\Ifirma::API_KEY_MIEJSCE_WYSTAWIENIA);
		Config::getInstance()->{Config::API_NAZWA_SERII_NUMERACJI} = \Configuration::get(\Ifirma::API_KEY_NAZWA_SERII_NUMERACJI);
		return $this;
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function getSupportedActionTypes(){
		return array(
			self::KEY_ACTION_BILL,
			self::KEY_ACTION_INVOICE,
			self::KEY_ACTION_INVOICE_FROM_PROFORMA,
			self::KEY_ACTION_INVOICE_PROFORMA,
			self::KEY_ACTION_INVOICE_SEND
		);
	}
	
	/**
	 * 
	 * @param string $id
	 * @param string $type
	 * @return \ifirma\SendResponse
	 */
	public function sendInvoice($id, $type){
		if(!is_numeric($id) || !in_array($type, self::getSupportedActionTypes())){
			return SendResult::makeInvalidResponse(SendResult::MESSAGE_INVALID_USE);
		}
		
		$this->setConfiguration();
		
		if($type === self::KEY_ACTION_INVOICE_FROM_PROFORMA){
			return $this->_sendInvoiceBasedOnProforma($id);
		}
		
		return $this->_sendInvoice($id, $type);
	}
	
	/**
	 * 
	 * @param string $id
	 * @param string $type
	 * @return \ifirma\SendResult
	 */
	private function _sendInvoice($id, $type){
		$invoice = InvoiceBuilder::factory($id, $type);
		
		if($invoice === null){
			return SendResult::makeInvalidResponse(SendResult::MESSAGE_INVALID_USE);
		}
		$sendResponse = $invoice->send();

		if(!$sendResponse->isOk()){
			return SendResult::makeInvalidResponse(
				$sendResponse->getMessage() === null
				?
				SendResult::MESSAGE_UNABLE_TO_SEND_INVOICE
				:
				$sendResponse->getMessage()
			);
		}
		
		$responseContent = $sendResponse->getContent();
		$invoiceMap = $this->_createAndSaveInvoiceMapObject(
				$responseContent[Response::KEY_INVOICE_ID], $id, $this->_getInvoiceMapType($type)
				);
		if($invoiceMap === null){
			return SendResult::makeInvalidResponse(SendResult::MESSAGE_SEND_OK_UNABLE_TO_SAVE);
		}
		
		$newInvoiceObj = $invoice::get($responseContent[Response::KEY_INVOICE_ID]);
		$invoiceMap->updateInvoiceNumber(Invoice::filterNumber($newInvoiceObj->{InvoiceResponse::KEY_PELNY_NUMER}));
		
		$this->_comparePricesOnInvoices($newInvoiceObj, $id);
		
		
		return SendResult::makeValidResponse(
			$sendResponse->getMessage() === null
			?
			SendResult::MESSAGE_INVOICE_SUCCESSFULLY_SEND
			:
			$sendResponse->getMessage()
		);
	}
	
	/**
	 * 
	 * @param mixed $ifirmaInvoice
	 * @param int $orderId
	 * @return void
	 */
	private function _comparePricesOnInvoices($ifirmaInvoice, $orderId){
		$order = new \Order((int)$orderId);
		if(!\Validate::isLoadedObject($order)){
			return;
		}
		
		$invoiceTotal = 0;
		foreach($ifirmaInvoice->{InvoiceAbstract::KEY_POZYCJE} as $invoiceEntry){
			$invoiceTotal += $invoiceEntry->{InvoicePosition::KEY_ILOSC} * intval($invoiceEntry->{InvoicePosition::KEY_CENA_Z_RABATEM} * 100);
		}
		
		if(intval(round($order->total_paid * 100)) !== $invoiceTotal){
			InternalComunicationManager::getInstance()->{InternalComunicationManager::KEY_INVOICE_VALIDATION_MESAGE}
			 = "Wykryto drobne nieprawidłowości. Możliwa przyczyna: różnica w sposobie zaokrąglania kwoty podatku lub naliczenie niebsługiwanego rabatu. " .
			"Proszę dokonać weryfikacji poprawności wystawionej faktury z poziomu serwisu <a target=\"_blank\" href=\"http://www.ifirma.pl\">ifirma.pl</a>";
		}
	}
	
	/**
	 * 
	 * @param int $invoiceMapId
	 * @return \ifirma\SendResult
	 */
	private function _sendInvoiceBasedOnProforma($invoiceMapId){
		
		$invoiceMap = InvoiceMap::get($invoiceMapId);

		if($invoiceMap === null){
			return SendResult::makeInvalidResponse(SendResult::MESSAGE_MISSING_INVOICE_PROFORMA);
		}

		$invoiceResponseObj = $this->_getInvoiceResponseObject($invoiceMap);
		if($invoiceResponseObj === null){
			return SendResult::makeInvalidResponse(SendResult::MESSAGE_MISSING_INVOICE_PROFORMA);
		}

		$sendInvoiceResult = $invoiceResponseObj->sendInvoiceBasedOnThisProforma();
		if(!$sendInvoiceResult->isOk()){
			return SendResult::makeInvalidResponse(
				$sendInvoiceResult->getMessage() !== ''
				?
				$sendInvoiceResult->getMessage()
				:
				SendResult::MESSAGE_UNABLE_TO_SEND_INVOICE
			);
		}
		
		$sendInvoiceResultContent = $sendInvoiceResult->getContent();
		$newInvoiceId = $sendInvoiceResultContent[Response::KEY_INVOICE_ID];
		$newInvoiceMapObj = $this->_createAndSaveInvoiceMapObject($newInvoiceId, $invoiceMap->{InvoiceMap::COLUMN_NAME_ORDER_ID}, InvoiceMap::INVOICE_TYPE_NORMAL);
		$newInvoiceObj = Invoice::get($newInvoiceId);
		$newInvoiceMapObj->updateInvoiceNumber(Invoice::filterNumber($newInvoiceObj->{InvoiceResponse::KEY_PELNY_NUMER}));
		
		return SendResult::makeValidResponse(
			$sendInvoiceResult->getMessage() !== ''
			?
			$sendInvoiceResult->getMessage()
			:
			SendResult::MESSAGE_INVOICE_SUCCESSFULLY_SEND
		);
	}
	
	
	/**
	 * 
	 * @param int $invoiceId
	 * @param int $orderId
	 * @param string $invoiceType
	 * @return InvoiceMap
	 */
	private function _createAndSaveInvoiceMapObject($invoiceId, $orderId, $invoiceType){
		$invoiceMap = new InvoiceMap();
		
		$invoiceMap->{InvoiceMap::COLUMN_NAME_ORDER_ID} = $orderId;
		$invoiceMap->{InvoiceMap::COLUMN_NAME_INVOICE_ID} = $invoiceId;
		$invoiceMap->{InvoiceMap::COLUMN_NAME_INVOICE_TYPE} = $invoiceType;
		
		return $invoiceMap->save();
	}
	
	/**
	 * 
	 * @param string $actionType
	 * @return string
	 */
	private function _getInvoiceMapType($actionType){
		switch($actionType){
			case self::KEY_ACTION_INVOICE:
				return InvoiceMap::INVOICE_TYPE_NORMAL;
				break;
			case self::KEY_ACTION_BILL:
				return InvoiceMap::INVOICE_TYPE_BILL;
				break;
			case self::KEY_ACTION_INVOICE_PROFORMA:
				return InvoiceMap::INVOICE_TYPE_PROFORMA;
				break;
			case self::KEY_ACTION_INVOICE_SEND:
				return InvoiceMap::INVOICE_TYPE_SEND;
				break;
		}
	}
}

