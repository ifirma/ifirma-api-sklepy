<?php

require_once dirname(__FILE__) . '/StrategyBill.php';
require_once dirname(__FILE__) . '/StrategyInvoice.php';
require_once dirname(__FILE__) . '/StrategyInvoiceProforma.php';
require_once dirname(__FILE__) . '/StrategyInvoiceSend.php';

/**
 * Description of InvoiceBuilder
 *
 * @author bbojanowicz
 */
class InvoiceBuilder {
	
	/**
	 * 
	 * @param int $orderId
	 * @param string $type
	 * @return \ifirma\InvoiceAbstract|null
	 */
	public static function factory($order, $type){
		$strategy = self::_getStrategy($type);

		$strategy->setOrder($order);
		
		return $strategy->makeInvoice();
	}
	
	/**
	 * 
	 * @param string $type
	 * @return StrategyAbstract
	 */
	private static function _getStrategy($type){
		require_once dirname(__FILE__) . '/../manager/ApiManager.php';
		
		switch($type){
			case ApiManager::KEY_ACTION_BILL:
				return new StrategyBill();
				break;
			
			case ApiManager::KEY_ACTION_INVOICE:
				return new StrategyInvoice();
				break;
			
			case ApiManager::KEY_ACTION_INVOICE_PROFORMA:
				return new StrategyInvoiceProforma();
				break;
			
			case ApiManager::KEY_ACTION_INVOICE_SEND:
				return new StrategyInvoiceSend();
				break;
		}
	}
}

