<?php

namespace ifirma;

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
	public static function factory($orderId, $type){
		$strategy = self::_getStrategy($type);
		$order = new \Order((int)$orderId);
		
		if(!\Validate::isLoadedObject($order)){
			return null;
		}
		
		$strategy->setOrder($order);
		
		$address = new \Address($order->id_address_invoice);
		if(!\Validate::isLoadedObject($address)){
			return null;
		}
		$strategy->setAddress($address);
		
		$customer = new \Customer($order->id_customer);
		if(!\Validate::isLoadedObject($customer)){
			return null;
		}
		$strategy->setCustomer($customer);
		
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

