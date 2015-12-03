<?php

require_once dirname(__FILE__) . '/Connector/Invoice/Invoice.php';
require_once dirname(__FILE__) . '/Connector/Invoice/InvoiceBill.php';
require_once dirname(__FILE__) . '/Connector/Invoice/InvoiceSend.php';
require_once dirname(__FILE__) . '/Connector/Invoice/InvoiceProforma.php';
require_once dirname(__FILE__) . '/Connector/Invoice/InvoiceResponse.php';
require_once dirname(__FILE__) . '/Connector/Invoice/InvoiceSendResponse.php';
require_once dirname(__FILE__) . '/Connector/Invoice/InvoiceProformaResponse.php';
require_once dirname(__FILE__) . '/Connector/Invoice/InvoiceBillResponse.php';
require_once dirname(__FILE__) . '/Connector/Response.php';

/**
 * Description of ApiManager
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Model_ApiManager {
	
	const KEY_ACTION_INVOICE = 'inv';
	const KEY_ACTION_INVOICE_SEND = 'invsend';
	const KEY_ACTION_INVOICE_PROFORMA = 'invpro';
	const KEY_ACTION_INVOICE_FROM_PROFORMA = 'invfinvpro';
	const KEY_ACTION_BILL = 'bill';
	
	const UNEXISTING_PDF_NAME = 'nieistnieje.pdf';
	const PDF_NAME_PREFIX_INVOICE = 'Faktura';
	const PDF_NAME_PREFIX_BILL = 'Rachunek';
	
	private static $instance;
	private $ifirmaInvoiceMapper;
	
	private function __construct() {}
	
	public static function getInstance(){
		if(self::$instance === null){
			self::$instance = new self();
		}
		
		return self::$instance;
	}
	
	public function getIfirmaInvoiceMapper(){
		if($this->ifirmaInvoiceMapper === null){
			$this->ifirmaInvoiceMapper = new PowerMedia_Ifirma_Model_Mapper_IfirmaInvoiceMapper();
		}
		
		return $this->ifirmaInvoiceMapper;
	}
	
	public function getDocumentPdfName($invoiceMapId){
		$invoiceMap = $this->getIfirmaInvoiceMapper()->getIfirmaInvoiceMapModel(intval($invoiceMapId));
		
		if($invoiceMap === null){
			return self::UNEXISTING_PDF_NAME;
		}
		
		return sprintf(
			"%s_%s.pdf",
			(
				$invoiceMap->getInvoiceType() === PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_BILL
				?
				self::PDF_NAME_PREFIX_BILL
				:
				self::PDF_NAME_PREFIX_INVOICE
			),
			$invoiceMap->getFilteredNumber()
		);
	}
	
	public function getDocumentAsPdf($invoiceMapId){
		PowerMedia_Ifirma_Model_Configuration::getInstance()->setConfiguration();
		$invoiceMap = $this->getIfirmaInvoiceMapper()->getIfirmaInvoiceMapModel(intval($invoiceMapId));
		
		if($invoiceMap === null){
			return null;
		}
		
		$invoiceResponseObj = $this->getInvoiceResponseObject($invoiceMap);
		if($invoiceResponseObj === null){
			return null;
		}
		return $invoiceResponseObj->getPdf($invoiceMap->getInvoiceId());
	}
	
	private function getInvoiceResponseObject($invoiceMap){
		switch($invoiceMap->getInvoiceType()){
			case PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE:
				return ifirma\Invoice::get($invoiceMap->getInvoiceId());
				break;
			
			case PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_SEND:
				return ifirma\InvoiceSend::get($invoiceMap->getInvoiceId());
				break;
			
			case PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_PROFORMA:
				return ifirma\InvoiceProforma::get($invoiceMap->getInvoiceId());
				break;
			
			case PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_BILL:
				return ifirma\InvoiceBill::get($invoiceMap->getInvoiceId());
				break;
		}
	}
	
	/**
	 * 
	 * @param int $orderId
	 * @param string $type
	 * @return PowerMedia_Ifirma_Model_SendResult
	 */
	public function sendInvoice($orderId, $type){
		if(!is_numeric($orderId) || !in_array($type, self::getSupportedActionTypes())){
			return PowerMedia_Ifirma_Model_SendResult::makeInvalidResponse(PowerMedia_Ifirma_Model_SendResult::MESSAGE_INVALID_USE);
		}
		
		PowerMedia_Ifirma_Model_Configuration::getInstance()->setConfiguration();
		
		if($type === self::KEY_ACTION_INVOICE_FROM_PROFORMA){
			return $this->sendInvoiceBasedOnProformaToIfirma($orderId);
		}
		
		return $this->sendInvoiceToIfirma($orderId, $type);
	}
	
	/**
	 * 
	 * @param int $orderId
	 * @param string $type
	 * @return PowerMedia_Ifirma_Model_SendResult
	 */
	private function sendInvoiceToIfirma($orderId, $type){
		$order = Mage::getModel('sales/order')->load($orderId);
		$invoice = PowerMedia_Ifirma_Model_Builder_InvoiceBuilder::factory($order, $type);
		if(!$invoice->isValid()){
			return PowerMedia_Ifirma_Model_SendResult::makeInvalidResponse(
				sprintf("%s %s",
					PowerMedia_Ifirma_Model_SendResult::MESSAGE_UNABLE_TO_SEND_INVOICE,
					$invoice->getFirstValidationErrorMessage()
				)
			);
		}
		
		try{
			$sendResponse = $invoice->send();
		}catch(ifirma\IfirmaException $e){
			return PowerMedia_Ifirma_Model_SendResult::makeInvalidResponse(
				PowerMedia_Ifirma_Model_SendResult::MESSAGE_UNEXPEXTED_ERROR
			);
		}
		
		if(!$sendResponse->isOk()){
			return PowerMedia_Ifirma_Model_SendResult::makeInvalidResponse(
				$sendResponse->getMessage() === null
				?
				PowerMedia_Ifirma_Model_SendResult::MESSAGE_UNABLE_TO_SEND_INVOICE
				:
				$sendResponse->getMessage()
			);
		}
		
		$responseContent = $sendResponse->getContent();
		$invoiceMap = $this->getIfirmaInvoiceMapper()->creteAndSaveNewIfirmaInvoiceMapModel(
				$orderId, $responseContent[ifirma\Response::KEY_INVOICE_ID], $this->getInvoiceMapType($type)
		);
		
		$newInvoiceObj = $invoice::get($responseContent[ifirma\Response::KEY_INVOICE_ID]);
		$this->getIfirmaInvoiceMapper()->updateInvoiceNumber($invoiceMap, $newInvoiceObj->{ifirma\InvoiceResponse::KEY_PELNY_NUMER});
		
		$this->comparePricesOnInvoices($newInvoiceObj, $order);
		
		return PowerMedia_Ifirma_Model_SendResult::makeValidResponse(
			$sendResponse->getMessage() === null
			?
			PowerMedia_Ifirma_Model_SendResult::MESSAGE_INVOICE_SUCCESSFULLY_SEND
			:
			$sendResponse->getMessage()
		);
	}

	/**
	 * 
	 * @param type $ifirmaInvoice
	 * @param type $order
	 */
	private function comparePricesOnInvoices($ifirmaInvoice, $order){
		$invoiceTotal = 0;
		foreach($ifirmaInvoice->{ifirma\InvoiceAbstract::KEY_POZYCJE} as $invoiceEntry){
			$invoiceTotal += $invoiceEntry->{ifirma\InvoicePosition::KEY_ILOSC} * intval($invoiceEntry->{ifirma\InvoicePosition::KEY_CENA_Z_RABATEM} * 100);
		}
		
		if(intval($order->getGrandTotal() * 100) !== $invoiceTotal){
			Mage::getSingleton('core/session')->addWarning(
				"Wykryto drobne nieprawidlowości. Możliwa przyczyna: różnica w sposobie zaokrąglania kwoty podatku lub naliczenie niebsługiwanego rabatu. " .
				"Proszę dokonać weryfikacji poprawności wystawionej faktury z poziomu serwisu <a target=\"_blank\" href=\"http://www.ifirma.pl\">ifirma.pl</a>"
			);
		}
	}
	
	/**
	 * 
	 * @param int $orderId
	 * @return PowerMedia_Ifirma_Model_SendResponse
	 */
	private function sendInvoiceBasedOnProformaToIfirma($orderId){
		$invoiceMap = $this->getIfirmaInvoiceMapper()->getInvoiceProformaMapModel($orderId);
		if($invoiceMap === null){
			return PowerMedia_Ifirma_Model_SendResult::makeInvalidResponse(
				PowerMedia_Ifirma_Model_SendResult::MESSAGE_MISSING_INVOICE_PROFORMA
			);
		}
		
		$invoiceResponseObj = $this->getInvoiceResponseObject($invoiceMap);
		if($invoiceResponseObj === null){
			return PowerMedia_Ifirma_Model_SendResult::makeInvalidResponse(
				PowerMedia_Ifirma_Model_SendResult::MESSAGE_MISSING_INVOICE_PROFORMA
			);
		}
		
		try{
			$sendInvoiceResult = $invoiceResponseObj->sendInvoiceBasedOnThisProforma();
		}catch(ifirma\IfirmaException $e){
			return PowerMedia_Ifirma_Model_SendResult::makeInvalidResponse(
				PowerMedia_Ifirma_Model_SendResult::MESSAGE_UNEXPEXTED_ERROR
			);
		}
		
		if(!$sendInvoiceResult->isOk()){
			return PowerMedia_Ifirma_Model_SendResult::makeInvalidResponse(
				$sendInvoiceResult->getMessage() !== ''
				?
				$sendInvoiceResult->getMessage()
				:
				PowerMedia_Ifirma_Model_SendResult::MESSAGE_UNABLE_TO_SEND_INVOICE
			);
		}
		
		$sendInvoiceResultContent = $sendInvoiceResult->getContent();
		$newInvoiceId = $sendInvoiceResultContent[ifirma\Response::KEY_INVOICE_ID];
		$newInvoiceMapObj = $this->getIfirmaInvoiceMapper()->creteAndSaveNewIfirmaInvoiceMapModel(
			$orderId, 
			$newInvoiceId, 
			PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE
		);
		$newInvoiceObj = ifirma\Invoice::get($newInvoiceId);
		$this->getIfirmaInvoiceMapper()->updateInvoiceNumber(
			$newInvoiceMapObj, 
			$newInvoiceObj->{ifirma\InvoiceResponse::KEY_PELNY_NUMER}
		);
		
		return PowerMedia_Ifirma_Model_SendResult::makeValidResponse(
			$sendInvoiceResult->getMessage() !== ''
			?
			$sendInvoiceResult->getMessage()
			:
			PowerMedia_Ifirma_Model_SendResult::MESSAGE_INVOICE_SUCCESSFULLY_SEND
		);
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function getSupportedActionTypes(){
		return array(
			self::KEY_ACTION_INVOICE,
			self::KEY_ACTION_INVOICE_SEND,
			self::KEY_ACTION_INVOICE_PROFORMA,
			self::KEY_ACTION_INVOICE_FROM_PROFORMA,
			self::KEY_ACTION_BILL
		);
	}
	
	/**
	 * 
	 * @param string $actionType
	 * @return string
	 */
	private function getInvoiceMapType($actionType){
		switch($actionType){
			case self::KEY_ACTION_INVOICE:
				return PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE;
				break;
			
			case self::KEY_ACTION_INVOICE_SEND:
				return PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_SEND;
				break;
			
			case self::KEY_ACTION_INVOICE_PROFORMA:
				return PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_PROFORMA;
				break;
			
			case self::KEY_ACTION_BILL:
				return PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_BILL;
				break;
			
			default:
				throw new Zend_Exception("Unsupported action type: $actionType.");
				break;
		}
	}
}

