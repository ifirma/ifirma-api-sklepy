<?php

/**
 * Description of IfirmaController
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_InvoiceController extends Mage_Adminhtml_Controller_Action{
	
	public function sendAction(){
		$orderId = $this->getRequest()->getParam('order_id');
		$type = $this->getRequest()->getParam('type');
		
		$sendResult = PowerMedia_Ifirma_Model_ApiManager::getInstance()->sendInvoice($orderId, $type);
		
		if($sendResult->isOk()){
			Mage::getSingleton('core/session')->addSuccess(
				$sendResult->getMessage() === ''
				?
				$this->__('Pomyślnie wystawiono fakturę.')
				:
				$sendResult->getMessage()
			);
		} else {
			Mage::getSingleton('core/session')->addError($sendResult->getMessage());
		}
		
		$this->_redirectReferer();
	}
	
	public function getAction(){
		$id = $this->getRequest()->getParam('id');
		
		$this->getResponse()->setHeader('Content-Type', 'application/pdf');
		$this->getResponse()->setheader('Content-disposition', 'attachment; filename="'.  PowerMedia_Ifirma_Model_ApiManager::getInstance()->getDocumentPdfName($id).'"');
		$this->getResponse()->setBody(PowerMedia_Ifirma_Model_ApiManager::getInstance()->getDocumentAsPdf($id));
	}
}

