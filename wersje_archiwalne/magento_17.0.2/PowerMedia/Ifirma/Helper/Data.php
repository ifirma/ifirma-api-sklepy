<?php

/**
 * Description of Data
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Helper_Data extends Mage_Core_Helper_Abstract{
	
	private $order;
	
	public function enableToGenerateInvoice(){
		return PowerMedia_Ifirma_Model_Configuration::getInstance()->isVAT()
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceMapModel($this->getOrder()->getId()) === null
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceSendMapModel($this->getOrder()->getId()) === null
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceProformaMapModel($this->getOrder()->getId()) === null
		;
	}
	
	public function enableToGenerateInvoiceBasedOnProforma(){
		return PowerMedia_Ifirma_Model_Configuration::getInstance()->isVAT()
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceMapModel($this->getOrder()->getId()) === null
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceSendMapModel($this->getOrder()->getId()) === null
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceProformaMapModel($this->getOrder()->getId()) !== null;
			
	}
	
	public function enableToGenerateInvoiceSend(){
		return PowerMedia_Ifirma_Model_Configuration::getInstance()->isVAT()
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceMapModel($this->getOrder()->getId()) === null
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceSendMapModel($this->getOrder()->getId()) === null
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceProformaMapModel($this->getOrder()->getId()) === null
		;
	}
	
	public function enableToGenerateInvoiceProforma(){
		return $this->enableToGenerateInvoice()
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceProformaMapModel($this->getOrder()->getId()) === null;
		;
	}
	
	public function enableToGenerateBill(){
		return !PowerMedia_Ifirma_Model_Configuration::getInstance()->isVAT()
				&&
				PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceBillMapModel($this->getOrder()->getId()) === null;
	}
	
	public function enableToGetInvoice(){ 
		return PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceMapModel($this->getOrder()->getId()) !== null;
	}
	
	public function enableToGetInvoiceSend(){
		return PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceSendMapModel($this->getOrder()->getId()) !== null;
	}
	
	public function enableToGetInvoiceProforma(){
		return PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceProformaMapModel($this->getOrder()->getId()) !== null;
	}
	
	public function enableToGetBill(){
		return PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
				->getInvoiceBillMapModel($this->getOrder()->getId()) !== null;
	}
	
	public function getSendInvoiceUrl(){
		return $this->getSendUrl(PowerMedia_Ifirma_Model_ApiManager::KEY_ACTION_INVOICE);
	}
	
	public function getSendInvoiceBasedOnProformaUrl(){
		return $this->getSendUrl(PowerMedia_Ifirma_Model_ApiManager::KEY_ACTION_INVOICE_FROM_PROFORMA);
	}
	
	public function getGetInvoiceUrl(){
		return Mage::getModel('adminhtml/url')->getUrl(
			'ifirma/invoice/get',
			array(
				'id' => PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
					->getInvoiceMapModel($this->getOrder()->getId())->getId()
			)
		);
	}
	
	public function getSendInvoiceSendUrl(){
		return $this->getSendUrl(PowerMedia_Ifirma_Model_ApiManager::KEY_ACTION_INVOICE_SEND);
	}
	
	public function getGetInvoiceSendUrl(){
		return Mage::getModel('adminhtml/url')->getUrl(
			'ifirma/invoice/get',
			array(
				'id' => PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
					->getInvoiceSendMapModel($this->getOrder()->getId())->getId()
			)
		);
	}
	
	public function getSendInvoiceProformaUrl(){
		return $this->getSendUrl(PowerMedia_Ifirma_Model_ApiManager::KEY_ACTION_INVOICE_PROFORMA);
	}
	
	public function getGetInvoiceProformaUrl(){
		return Mage::getModel('adminhtml/url')->getUrl(
			'ifirma/invoice/get',
			array(
				'id' => PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
					->getInvoiceProformaMapModel($this->getOrder()->getId())->getId()
			)
		);
	}
	
	public function getSendInvoiceBillUrl(){
		return $this->getSendUrl(PowerMedia_Ifirma_Model_ApiManager::KEY_ACTION_BILL);
	}
	
	public function getGetInvoiceBillUrl(){
		return Mage::getModel('adminhtml/url')->getUrl(
			'ifirma/invoice/get',
			array(
				'id' => PowerMedia_Ifirma_Model_ApiManager::getInstance()->getIfirmaInvoiceMapper()
					->getInvoiceBillMapModel($this->getOrder()->getId())->getId()
			)
		);
	}
	
	private function getOrder(){
		if($this->order === null){
			$this->order = Mage::registry('current_order');
		}
		
		return $this->order;
	}
	
	private function getSendUrl($type){
		return Mage::getModel('adminhtml/url')->getUrl(
			'ifirma/invoice/send',
			array(
				'order_id'	=> $this->getOrder()->getId(),
				'type'		=> $type
			)
		);
	}
}

