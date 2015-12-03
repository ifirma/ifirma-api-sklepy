<?php

/**
 * Description of InvoiceBuilder
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Model_Builder_InvoiceBuilder {
	
	public static function factory($order, $type){
		$strategy = self::getStrategy($type);
		
		$strategy->setOrder($order);
		
		return $strategy->makeInvoice();
	}
	
	/**
	 * 
	 * @param string $type
	 * @return \PowerMedia_Ifirma_Model_Builder_StrategyBill|\PowerMedia_Ifirma_Model_Builder_StrategyInvoice|\PowerMedia_Ifirma_Model_Builder_StrategyInvoiceSend|\PowerMedia_Ifirma_Model_Builder_StrategyProforma
	 */
	private static function getStrategy($type){
		switch($type){
			case PowerMedia_Ifirma_Model_ApiManager::KEY_ACTION_BILL:
				return new PowerMedia_Ifirma_Model_Builder_StrategyBill();
				break;
			
			case PowerMedia_Ifirma_Model_ApiManager::KEY_ACTION_INVOICE:
				return new PowerMedia_Ifirma_Model_Builder_StrategyInvoice();
				break;
			
			case PowerMedia_Ifirma_Model_ApiManager::KEY_ACTION_INVOICE_SEND:
				return new PowerMedia_Ifirma_Model_Builder_StrategyInvoiceSend();
				break;
			
			case PowerMedia_Ifirma_Model_ApiManager::KEY_ACTION_INVOICE_PROFORMA:
				return new PowerMedia_Ifirma_Model_Builder_StrategyProforma();
				break;
		}
	}
}

