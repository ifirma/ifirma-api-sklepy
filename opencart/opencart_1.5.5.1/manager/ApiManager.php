<?php

require_once dirname(__FILE__) . '/../admin/model/module/invoice_map.php';
require_once dirname(__FILE__) . '/../admin/model/setting/setting.php';
require_once dirname(__FILE__) . '/../connector/GetPdfInterface.php';
require_once dirname(__FILE__) . '/../connector/ConnectorConfig.php';
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
         * @param string $hash
	 * @return bool
	 */
	public function checkIfirmaHash($url_hash, $settings_hash){
		return ($url_hash == $settings_hash);
	}
	
	/**
	 * 
	 * @param int $invoiceMapId
	 * @return string|null
	 */
	public function getDocumentAsPdf($invoiceMap){
		$this->setConfiguration();
		
		if($invoiceMap === null){
			return null;
		}
		
		$invoiceResponseObj = $this->_getInvoiceResponseObject($invoiceMap);
		if($invoiceResponseObj === null){
			return null;
		}
		
		return $invoiceResponseObj->getPdf();
	}
	
	/**
	 * 
	 * @param ModelModuleInvoiceMap $invoiceMap
	 * @return GetPdfInterface
	 */
	private function _getInvoiceResponseObject( $invoiceMap){
		switch($invoiceMap[ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_TYPE]){
			case ModelModuleInvoiceMap::INVOICE_TYPE_BILL:
				return InvoiceBill::get($invoiceMap[ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_ID]);
				break;
			
			case ModelModuleInvoiceMap::INVOICE_TYPE_NORMAL:
				return Invoice::get($invoiceMap[ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_ID]);
				break;
			
			case ModelModuleInvoiceMap::INVOICE_TYPE_PROFORMA:
				return InvoiceProforma::get($invoiceMap[ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_ID]);
				break;
			
			case ModelModuleInvoiceMap::INVOICE_TYPE_SEND:
				return InvoiceSend::get($invoiceMap[ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_ID]);
				break;
		}
	}
	
	/**
	 * 
	 * @param int $invoiceMapId
	 * @return string
	 */
	public function getDocumentPdfName($invoiceMap){		
		if($invoiceMap === null){
			return self::UNEXISTING_PDF_NAME;
		}
		
		return sprintf(
			"%s_%s.pdf",
			(
				$invoiceMap[ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_TYPE] === ModelModuleInvoiceMap::INVOICE_TYPE_BILL
				?
				self::PDF_NAME_PREFIX_BILL		
				:
				self::PDF_NAME_PREFIX_INVOICE
			),
			$invoiceMap[ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_NUMBER]
			);
	}
	
	/**
	 * 
	 * @return \ifirma\ApiManager
	 */
	public function setConfiguration(){
                global $loader, $registry;
                $loader->model('setting/setting');
                $settings = $registry->get('model_setting_setting');
		$ifi_invoice = $settings->getSetting('ifi_invoice');
		ConnectorConfig::getInstance()->{ConnectorConfig::API_LOGIN} = $ifi_invoice[ModelModuleInvoiceMap::API_LOGIN];
		ConnectorConfig::getInstance()->{ConnectorConfig::API_KEY_BILL} = $ifi_invoice[ModelModuleInvoiceMap::API_KEY_BILL];
		ConnectorConfig::getInstance()->{ConnectorConfig::API_KEY_INVOICE} = $ifi_invoice[ModelModuleInvoiceMap::API_KEY_INVOICE];
		ConnectorConfig::getInstance()->{ConnectorConfig::API_KEY_SUBSCRIBER} = $ifi_invoice[ModelModuleInvoiceMap::API_KEY_SUBSCRIBER];
		
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
	public function sendInvoice($order, $type){
		if(!$order || !in_array($type, self::getSupportedActionTypes())){
			return SendResult::makeInvalidResponse(SendResult::MESSAGE_INVALID_USE);
		}
		
		$this->setConfiguration();
		
		if($type === self::KEY_ACTION_INVOICE_FROM_PROFORMA){
			return $this->_sendInvoiceBasedOnProforma($order["order_id"]);
		}
		try{
                   return $this->_sendInvoice($order, $type); 
                }catch(Exception $e){
                    return SendResult::makeInvalidResponse(SendResult::MESSAGE_INVALID_KEYS);
                }
	}
	
	/**
	 * 
	 * @param Order $order
	 * @param string $type
	 * @return \ifirma\SendResult
	 */
	private function _sendInvoice($order, $type){
                $invoice = InvoiceBuilder::factory($order, $type);
		
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
		$newInvoiceObj = $invoice::get($responseContent[ConnectorResponse::KEY_INVOICE_ID]);

		$invoiceMap = $this->_createAndSaveInvoiceMapObject(
                        $responseContent[ConnectorResponse::KEY_INVOICE_ID], $order["order_id"], $this->_getInvoiceMapType($type), Invoice::filterNumber($newInvoiceObj->{InvoiceResponse::KEY_PELNY_NUMER})
		);
		if($invoiceMap === null){
			return SendResult::makeInvalidResponse(SendResult::MESSAGE_SEND_OK_UNABLE_TO_SAVE);
		}

		$this->_comparePricesOnInvoices($newInvoiceObj, $order["order_id"]);
		
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
	private function _comparePricesOnInvoices($ifirmaInvoice, $order){
		
		$invoiceTotal = 0;
		foreach($ifirmaInvoice->{InvoiceAbstract::KEY_POZYCJE} as $invoiceEntry){
			$invoiceTotal += $invoiceEntry->{InvoicePosition::KEY_ILOSC} * intval($invoiceEntry->{InvoicePosition::KEY_CENA_Z_RABATEM} * 100);
		}
		
		if(intval($order->total_paid * 100) !== $invoiceTotal){
			InternalComunicationManager::getInstance()->{InternalComunicationManager::KEY_INVOICE_VALIDATION_MESAGE}
			 = "Wykryto drobne nieprawidlowosci. Możliwa przyczyna: rónica w sposobie zaokrąglania kwoty podatku lub naliczenie niebsługiwanego rabatu. " .
			"Proszę dokonać weryfikacji poprawności wystawionej faktury z poziomu serwisu <a target=\"_blank\" href=\"http://www.ifirma.pl\">ifirma.pl</a>";
		}
	}
	
	/**
	 * 
	 * @param int $invoiceMapId
	 * @return \ifirma\SendResult
	 */
	private function _sendInvoiceBasedOnProforma($invoiceMapId){
		global $loader, $registry;
                $loader->model('module/invoice_map');
                $invoiceMapModel = $registry->get('model_module_invoice_map');
		$invoiceMap = $invoiceMapModel->get($invoiceMapId);

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
		$newInvoiceId = $sendInvoiceResultContent[ConnectorResponse::KEY_INVOICE_ID];
      		$newInvoiceObj = Invoice::get($newInvoiceId);

		$this->_createAndSaveInvoiceMapObject($newInvoiceId, $invoiceMap[ModelModuleInvoiceMap::COLUMN_NAME_ORDER_ID], ModelModuleInvoiceMap::INVOICE_TYPE_NORMAL, Invoice::filterNumber($newInvoiceObj->{InvoiceResponse::KEY_PELNY_NUMER}));
//                $invoiceMapModel->updateInvoiceNumber($invoiceMap[ModelModuleInvoiceMap::COLUMN_NAME_ORDER_ID], Invoice::filterNumber($newInvoiceObj->{InvoiceResponse::KEY_PELNY_NUMER}));
		
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
	 * @return ModelModuleInvoiceMap
	 */
	private function _createAndSaveInvoiceMapObject($invoiceId, $orderId, $invoiceType, $invoiceNumber){
                global $loader, $registry;
                $loader->model('module/invoice_map');
                $invoiceMap = $registry->get('model_module_invoice_map');
		
		$invoiceMap->{ModelModuleInvoiceMap::COLUMN_NAME_ORDER_ID} = $orderId;
		$invoiceMap->{ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_ID} = $invoiceId;
		$invoiceMap->{ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_TYPE} = $invoiceType;
		$invoiceMap->{ModelModuleInvoiceMap::COLUMN_NAME_INVOICE_NUMBER} = $invoiceNumber;
                
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
				return ModelModuleInvoiceMap::INVOICE_TYPE_NORMAL;
				break;
			case self::KEY_ACTION_BILL:
				return ModelModuleInvoiceMap::INVOICE_TYPE_BILL;
				break;
			case self::KEY_ACTION_INVOICE_PROFORMA:
				return ModelModuleInvoiceMap::INVOICE_TYPE_PROFORMA;
				break;
			case self::KEY_ACTION_INVOICE_SEND:
				return ModelModuleInvoiceMap::INVOICE_TYPE_SEND;
				break;
		}
	}
}

