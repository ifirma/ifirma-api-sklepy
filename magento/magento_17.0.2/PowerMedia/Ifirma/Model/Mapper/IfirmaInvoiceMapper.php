<?php

require_once dirname(__FILE__) . '/../Connector/Invoice/Invoice.php';

/**
 * Description of IfirmaInvoiceMapper
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Model_Mapper_IfirmaInvoiceMapper {
	
	private $ifirmaInvoiceMapModel = array();
	private $ifirmaInvoiceMapModelForOrder = array();
	private $loadedMapModels = false;
	
	public function __construct() {
		;
	}
	
	public function creteAndSaveNewIfirmaInvoiceMapModel($orderId, $invoiceId, $invoiceType){
		$ifirmaInvoiceMapModel = Mage::getModel('ifirma/ifirma');
		
		$ifirmaInvoiceMapModel->setOrderId($orderId);
		$ifirmaInvoiceMapModel->setInvoiceId($invoiceId);
		$ifirmaInvoiceMapModel->setInvoiceType($invoiceType);
		
		$ifirmaInvoiceMapModel->save();
		return $ifirmaInvoiceMapModel;
	}
	
	public function updateInvoiceNumber($ifirmaInvoiceMapModel, $newInvoiceNumber){
		$ifirmaInvoiceMapModel->setInvoiceNumber(
			ifirma\Invoice::filterNumber($newInvoiceNumber)
		);
		
		$ifirmaInvoiceMapModel->save();
		return $ifirmaInvoiceMapModel;
	}
	
	public function getIfirmaInvoiceMapModel($id){
		if(!isset($this->ifirmaInvoiceMapModel[$id])){
			$ifirmaInvoiceMapModel = Mage::getModel('ifirma/ifirma');
			$ifirmaInvoiceMapModel->load($id);
			$this->ifirmaInvoiceMapModel[$id] = $ifirmaInvoiceMapModel;
		}
		
		return $this->ifirmaInvoiceMapModel[$id];
	}
	
	private function getMapModel($orderId, $type){
		if(!$this->loadedMapModels){
			$this->loadMapModels($orderId);
		}
		
		return $this->ifirmaInvoiceMapModelForOrder[$orderId][$type];
	}
	
	private function loadMapModels($orderId){
		$this->ifirmaInvoiceMapModelForOrder[$orderId] = array(
			PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE => null,
			PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_SEND => null,
			PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_PROFORMA => null,
			PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_BILL => null
		); 

		$collection = Mage::getModel('ifirma/ifirma')->getCollection()
				->addFilter('order_id', intval($orderId));
		foreach($collection as $model){
			$this->ifirmaInvoiceMapModel[$model->getId()] = $model;
			$this->ifirmaInvoiceMapModelForOrder[$orderId][$model->getInvoiceType()] = $model;
		}
		
		$this->loadedMapModels = true;
	}
	
	public function getInvoiceMapModel($orderId){
		return $this->getMapModel($orderId, PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE);
	}
	
	public function getInvoiceSendMapModel($orderId){
		return $this->getMapModel($orderId, PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_SEND);
	}
	
	public function getInvoiceProformaMapModel($orderId){
		return $this->getMapModel($orderId, PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_PROFORMA);
	}
	
	public function getInvoiceBillMapModel($orderId){
		return $this->getMapModel($orderId, PowerMedia_Ifirma_Model_Ifirma::TYPE_INVOICE_BILL);
	}
	
	public function invoiceMapModelExists($orderId){
		return $this->getInvoiceMapModel($orderId) !== null;
	}
	
	public function invoiceSendMapModelExists($orderId){
		return $this->getInvoiceSendMapModel($orderId) !== null;
	}
	
	public function invoiceProformaMapModelExists($orderId){
		return $this->getInvoiceProformaMapModel($orderId) !== null;
	}
	
	public function invoiceBillMapModelExists($orderId){
		return $this->getInvoiceBillMapModel($orderId) !== null;
	}
}

