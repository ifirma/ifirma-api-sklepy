<?php

/**
 * @author: Power Media S.A.
 * @release: 03.2013
 * @version: 1.0
 * @desc: Integracja z serwisem ifirma.pl dla PrestaShop v. 1.5.3
 */

if (!defined('_PS_VERSION_')){
	exit;
}

require_once dirname(__FILE__) . '/models/InvoiceMap.php';
require_once dirname(__FILE__) . '/manager/ApiManager.php';
require_once dirname(__FILE__) . '/manager/InternalComunicationManager.php';

/**
 * 
 */
class Ifirma extends Module {
	
	const API_VAT = 'ifirma_api_vat';
	const API_KEY_BILL = 'ifirma_api_key_bill';
	const API_KEY_INVOICE = 'ifirma_api_key_invoice';
	const API_KEY_SUBSCRIBER = 'ifirma_api_key_subscriber';
	const API_LOGIN = 'ifirma_api_login';
	const API_HASH = 'ifirma_hash';
	const API_HASH_LENGTH = 32;
	
	const SUBMIT_CONF_NAME = 'submitIfirmaSettings';
	
	const KEY_ORDER_ID = 'id_order';
	
	public function __construct() {
		$this->name = 'ifirma';
		$this->tab = 'billing_invoicing';
		$this->version = '1.0';
		$this->author = 'Power Media S.A.';
		$this->limited_countries = array('pl');

		parent::__construct();

		$this->displayName = 'Integracja z ifirma.pl';
		$this->description = 'Wystawiaj faktury w ifirma.pl';
		$this->confirmUninstall = 'Czy napewno usunąc integrację z serwisem ifirma.pl? Dane na temat historii wystawionych faktur zostaną bezpowrowtnie usunięte.';
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function install(){
		return
			parent::install()
			&&
			$this->registerHook('adminOrder')
			&&
			$this->_installDB()
			&&
			Configuration::updateValue(self::API_HASH,Tools::passwdGen(self::API_HASH_LENGTH))
			;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function uninstall(){
		return 
			parent::uninstall()
			&&
			$this->_uninstallDB()
			&&
			Configuration::deleteByName(self::API_VAT)
			&&
			Configuration::deleteByName(self::API_KEY_BILL)
			&&
			Configuration::deleteByName(self::API_KEY_INVOICE)
			&&
			Configuration::deleteByName(self::API_KEY_SUBSCRIBER)
			&&
			Configuration::deleteByName(self::API_LOGIN)
			&&
			Configuration::deleteByName(self::API_HASH)
			;
	}
	
	/**
	 * Module configuration
	 */
	public function getContent(){
		$processMessage = $this->_processConfiguration();
		
		$this->context->smarty->assign(array(
			'processMessage'	=> $processMessage,
			'formAction'		=> Tools::safeOutput($_SERVER['REQUEST_URI']),
			'adminImg'			=> _PS_ADMIN_IMG_,
			'submitName'		=> self::SUBMIT_CONF_NAME,
			'apiVatName'		=> self::API_VAT,
			'apiVatChecked'		=> (
									Tools::getValue(self::API_VAT, Configuration::get(self::API_VAT))
									?
									"checked=\"checked\""
									:
									""
									),
			'apiBillName'		=> self::API_KEY_BILL,
			'apiBillValue'		=> Tools::safeOutput(Tools::getValue(self::API_KEY_BILL, Configuration::get(self::API_KEY_BILL))),
			'apiInvoiceName'	=> self::API_KEY_INVOICE,
			'apiInvoiceValue'	=> Tools::safeOutput(Tools::getValue(self::API_KEY_INVOICE, Configuration::get(self::API_KEY_INVOICE))),
			'apiSubscriberName' => self::API_KEY_SUBSCRIBER,
			'apiSubscriberValue'=> Tools::safeOutput(Tools::getValue(self::API_KEY_SUBSCRIBER, Configuration::get(self::API_KEY_SUBSCRIBER))),
			'apiLoginName'		=> self::API_LOGIN,
			'apiLoginValue'		=> Tools::safeOutput(Tools::getValue(self::API_LOGIN, Configuration::get(self::API_LOGIN))),
		));
		
		return $this->display(__FILE__, 'views/conf.tpl');
	}
	
	/**
	 * 
	 * @return string
	 */
	private function _processConfiguration()
	{
		$message = '';
		if(Tools::isSubmit(self::SUBMIT_CONF_NAME))
		{
			Configuration::updateValue(self::API_VAT, Tools::getValue(self::API_VAT));
			Configuration::updateValue(self::API_KEY_BILL, Tools::getValue(self::API_KEY_BILL));
			Configuration::updateValue(self::API_KEY_INVOICE, Tools::getValue(self::API_KEY_INVOICE));
			Configuration::updateValue(self::API_KEY_SUBSCRIBER, Tools::getValue(self::API_KEY_SUBSCRIBER));
			Configuration::updateValue(self::API_LOGIN, Tools::getValue(self::API_LOGIN));
			
			$message = $this->displayConfirmation('Ustawienia zapisane');
		}
		
		return $message;
	}
	
	/**
	 * 
	 * @return bool
	 */
	private function _installDB(){
		return (bool)Db::getInstance()->execute(\ifirma\InvoiceMap::getInstallDBSql());
	}
	
	/**
	 * 
	 * @return bool
	 */
	private function _uninstallDB(){
		return (bool)Db::getInstance()->execute(\ifirma\InvoiceMap::getUninstallDBSql());
	}
	
	public function hookAdminOrder()
	{
		if(!$this->_isConfigurationSet()) 
		{
			return $this->display(__FILE__,'/views/noOrder.tpl');
		}
			
		$orderId = intval(Tools::getValue(self::KEY_ORDER_ID));
		if($orderId != 0){
			$invoiceMapCollection = \ifirma\InvoiceMap::getInvoiceMapRowsForOrderId($orderId);
			
			$this->context->smarty->assign(array(
				'sendResultMessage' => $this->_getSendResultMessage(),
				'invoiceValidationMessage' => $this->_getInvoiceValidationMessage(),
				'isVat' => Configuration::get(self::API_VAT),
				'hash' => Configuration::get(self::API_HASH),
				'orderId' => $orderId,
				'invoice' => $invoiceMapCollection->{\ifirma\InvoiceMap::INVOICE_TYPE_NORMAL},
				'invoiceSend' => $invoiceMapCollection->{\ifirma\InvoiceMap::INVOICE_TYPE_SEND},
				'invoiceProforma' => $invoiceMapCollection->{\ifirma\InvoiceMap::INVOICE_TYPE_PROFORMA},
				'bill' => $invoiceMapCollection->{\ifirma\InvoiceMap::INVOICE_TYPE_BILL},
				'actionInvoice' => \ifirma\ApiManager::KEY_ACTION_INVOICE,
				'actionInvoiceSend' => \ifirma\ApiManager::KEY_ACTION_INVOICE_SEND,
				'actionInvoiceProforma' => \ifirma\ApiManager::KEY_ACTION_INVOICE_PROFORMA,
				'actionInvoiceFromProforma' => \ifirma\ApiManager::KEY_ACTION_INVOICE_FROM_PROFORMA,
				'actionBill' => \ifirma\ApiManager::KEY_ACTION_BILL
			));
			
			return $this->display(__FILE__,'/views/order.tpl');
		}
		
		return '';
	}
	
	/**
	 * @return string
	 */
	private function _getInvoiceValidationMessage(){
		$valMessage = ifirma\InternalComunicationManager::getInstance()->{ifirma\InternalComunicationManager::KEY_INVOICE_VALIDATION_MESAGE};
		if($valMessage === null){
			return '';
		}
		
		return $this->displayWarning($valMessage);
	}
	
	/**
	 * 
	 * @param string $msg
	 * @return string
	 */
	public function displayWarning($msg){
		$output = '
		<div class="module_confirmation conf warn">
			'.$msg.'
		</div>';
		return $output;
	}
	
	/**
	 * @return string
	 */
	private function _getSendResultMessage(){
		$sendResult = ifirma\InternalComunicationManager::getInstance()->{ifirma\InternalComunicationManager::KEY_SEND_RESULT};
		if($sendResult === null){
			return '';
		}
		
		return (
				$sendResult->isOk()
				?
				$this->displayConfirmation($sendResult->getMessage())
				:
				$this->displayError($sendResult->getMessage())
				);
	}
	
	/**
	 * @return bool
	 */
	private function _isConfigurationSet(){
		return Configuration::get(self::API_LOGIN)
				&&
				Configuration::get(self::API_KEY_SUBSCRIBER)
				&&
				Configuration::get(self::API_KEY_INVOICE)
				&&
				Configuration::get(self::API_KEY_BILL)
				;
	}
}

?>
