<?php
//error_reporting(E_ALL);
//ini_set('display_errors',1);

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
	
	const MODULE_VERSION = '1.7';
	
	const API_VAT = 'ifirma_api_vat';
	const API_PODSTAWA_PRAWNA = 'ifirma_api_podstawa_prawna';
	const API_RYCZALT = 'ifirma_api_ryczalt';
	const API_RYCZALT_RATE = 'ifirma_api_ryczalt_rate';
	const API_RYCZALT_WPIS_DO_EWIDENCJI = 'ifirma_api_ryczalt_wpis_do_ewidencji';
	const API_KEY_BILL = 'ifirma_api_key_bill';
	const API_KEY_INVOICE = 'ifirma_api_key_invoice';
	const API_KEY_SUBSCRIBER = 'ifirma_api_key_subscriber';
	const API_LOGIN = 'ifirma_api_login';
	const API_HASH = 'ifirma_hash';
	const API_HASH_LENGTH = 32;
	
	const API_KEY_MIEJSCE_WYSTAWIENIA = 'ifirma_api_miasto_wystawienia';
	const API_KEY_NAZWA_SERII_NUMERACJI = 'ifirma_api_nazwa_serii_numeracji';
	
	const SUBMIT_CONF_NAME = 'submitIfirmaSettings';
	
	const KEY_ORDER_ID = 'id_order';
	
	//var $API_RYCZALT_RATES = array('3%', '5,5%', '8,5%', '17%', '20%');
	var $rates = array('0.03', '0.055', '0.085', '0.17', '0.2');
	var $rates_label = array('3%', '5,5%', '8,5%', '17%', '20%');
	
	public function __construct() {
		
		$this->name = 'ifirma';
		$this->tab = 'billing_invoicing';
		$this->version = self::MODULE_VERSION;
		$this->author = 'Power Media S.A.';
		$this->limited_countries = array('pl');

		parent::__construct();

		$this->displayName = 'Integracja z ifirma.pl';
		$this->description = 'Wystawiaj faktury w ifirma.pl';
		$this->confirmUninstall = 'Czy na pewno usunąć integrację z serwisem ifirma.pl? Dane na temat historii wystawionych faktur zostaną bezpowrotnie usunięte.';
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
			Configuration::deleteByName(self::API_PODSTAWA_PRAWNA)
			&&
			Configuration::deleteByName(self::API_RYCZALT)
			&&
			Configuration::deleteByName(self::API_RYCZALT_WPIS_DO_EWIDENCJI)
			&&
			Configuration::deleteByName(self::API_RYCZALT_RATE)
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
			&&
			Configuration::deleteByName(self::API_KEY_MIEJSCE_WYSTAWIENIA)
			&&
			Configuration::deleteByName(self::API_KEY_NAZWA_SERII_NUMERACJI)
			;
	}
	
	/**
	 * Module configuration
	 */
	private function removeWhitespaces($string){
			return preg_replace('/\s+/', '', $string);
	}
	

	public function getContent(){
		$processMessage = $this->_processConfiguration();
		$this->context->controller->addJS($this->_path.'js/ifirma.js');
		
		$this->context->smarty->assign(array(
			'processMessage'	=> $processMessage,
			'formAction'		=> Tools::safeOutput($_SERVER['REQUEST_URI']),
			'adminImg'			=> _PS_ADMIN_IMG_,
			'ifirmaImg'			=> _MODULE_DIR_.'ifirma/img/',
			'submitName'		=> self::SUBMIT_CONF_NAME,
			'apiVatName'		=> self::API_VAT,
			'apiVatChecked'		=> (
									Tools::getValue(self::API_VAT, Configuration::get(self::API_VAT))
									?
									"checked=\"checked\""
									:
									""
									),
			'apiPodstawaPrawnaName'	=> self::API_PODSTAWA_PRAWNA,
			'apiPodstawaPrawnaValue'		=> Tools::safeOutput(Tools::getValue(self::API_PODSTAWA_PRAWNA, Configuration::get(self::API_PODSTAWA_PRAWNA))), 
			'apiRyczaltName'		=> self::API_RYCZALT,
			'apiRyczaltChecked'		=> (
									Tools::getValue(self::API_RYCZALT, Configuration::get(self::API_RYCZALT))
									?
									"checked=\"checked\""
									:
									""
									),
			'apiRyczaltWpisDoEwidencji'=> self::API_RYCZALT_WPIS_DO_EWIDENCJI,
			'apiRyczaltWpisDoEwidencjiChecked'		=> (
									Tools::getValue(self::API_RYCZALT_WPIS_DO_EWIDENCJI, Configuration::get(self::API_RYCZALT_WPIS_DO_EWIDENCJI))
									?
									"checked=\"checked\""
									:
									""
									),
			'apiRyczaltRateName'=> self::API_RYCZALT_RATE,
			'apiRyczaltRateValue'=> Tools::safeOutput(Tools::getValue(self::API_RYCZALT_RATE, Configuration::get(self::API_RYCZALT_RATE))),
			'apiRyczaltRates'	=> $this->rates,
			'apiRyczaltRatesLabels'	=> $this->rates_label,
			
			'apiBillName'		=> self::API_KEY_BILL,
			
			'apiBillValue'		=> $this->removeWhitespaces(Tools::safeOutput(Tools::getValue(self::API_KEY_BILL, Configuration::get(self::API_KEY_BILL)))),
			
			'apiInvoiceName'	=> self::API_KEY_INVOICE,
			'apiInvoiceValue'	=> $this->removeWhitespaces(Tools::safeOutput(Tools::getValue(self::API_KEY_INVOICE, Configuration::get(self::API_KEY_INVOICE)))),
			'apiSubscriberName' => self::API_KEY_SUBSCRIBER,

			'apiSubscriberValue'=> $this->removeWhitespaces(Tools::safeOutput(Tools::getValue(self::API_KEY_SUBSCRIBER, Configuration::get(self::API_KEY_SUBSCRIBER)))),
			'apiLoginName'		=> self::API_LOGIN,
			'apiLoginValue'		=> Tools::safeOutput(Tools::getValue(self::API_LOGIN, Configuration::get(self::API_LOGIN))),
				
			'apiCityName'		=> self::API_KEY_MIEJSCE_WYSTAWIENIA,
			'apiCityValue'		=> Tools::safeOutput(Tools::getValue(self::API_KEY_MIEJSCE_WYSTAWIENIA, Configuration::get(self::API_KEY_MIEJSCE_WYSTAWIENIA))),
				
			'apiSeriesName'		=> self::API_KEY_NAZWA_SERII_NUMERACJI,
			'apiSeriesValue'	=> Tools::safeOutput(Tools::getValue(self::API_KEY_NAZWA_SERII_NUMERACJI, Configuration::get(self::API_KEY_NAZWA_SERII_NUMERACJI))),
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
			Configuration::updateValue(self::API_PODSTAWA_PRAWNA, Tools::getValue(self::API_PODSTAWA_PRAWNA));
			Configuration::updateValue(self::API_RYCZALT, Tools::getValue(self::API_RYCZALT));
			Configuration::updateValue(self::API_RYCZALT_WPIS_DO_EWIDENCJI, Tools::getValue(self::API_RYCZALT_WPIS_DO_EWIDENCJI));
			Configuration::updateValue(self::API_RYCZALT_RATE, Tools::getValue(self::API_RYCZALT_RATE));
			Configuration::updateValue(self::API_KEY_BILL, $this->removeWhitespaces(Tools::getValue(self::API_KEY_BILL)));
			Configuration::updateValue(self::API_KEY_INVOICE, $this->removeWhitespaces(Tools::getValue(self::API_KEY_INVOICE)));
			Configuration::updateValue(self::API_KEY_SUBSCRIBER, $this->removeWhitespaces(Tools::getValue(self::API_KEY_SUBSCRIBER)));
			Configuration::updateValue(self::API_LOGIN, Tools::getValue(self::API_LOGIN));
			Configuration::updateValue(self::API_KEY_MIEJSCE_WYSTAWIENIA, Tools::getValue(self::API_KEY_MIEJSCE_WYSTAWIENIA));
			Configuration::updateValue(self::API_KEY_NAZWA_SERII_NUMERACJI, Tools::getValue(self::API_KEY_NAZWA_SERII_NUMERACJI));
			
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
		$this->context->controller->addJS(($this->_path).'js/ifirma.js');
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
				'ifirmaImg'	=> _MODULE_DIR_.'ifirma/img/',
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
