<?php

require_once dirname(__FILE__) . '/../../../manager/ApiManager.php';
require_once dirname(__FILE__) . '/../../../manager/InternalComunicationManager.php';
require_once dirname(__FILE__) . '/../../model/module/invoice_map.php';

class ControllerModuleIfiInvoice extends Controller {

	private $error = array();
	
	public function index() {   
		//Load the language file for this module
		$this->load->language('module/ifi_invoice');

		//Set the title from the language file $_['heading_title'] string
		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');
		$this->load->model('module/invoice_map');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('ifi_invoice', array_merge($this->request->post, array(ModelModuleInvoiceMap::API_HASH => self::_generateHash(ModelModuleInvoiceMap::API_HASH_LENGTH))));

			$this->session->data['success'] = $this->language->get('text_success');

			if(version_compare(VERSION, '1.9.0.0', '>'))
				$this->response->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
			else
				$this->redirect($this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'));
		}

		$text_strings = array(
				'heading_title',
				'tab_ifirma',
				'is_vat_payer',
				'is_vat_payer_description',
				'api_key_bill',
				'api_key_bill_description',
				'api_key_invoice',
				'api_key_invoice_description',
				'api_key_subscriber',
				'api_key_subscriber_description',
				'api_login',
				'api_login_description',
				'save',
				'settings',
				'info',
				'text_success',
				'text_module',
				'visit',
				'warning_info',
				'button_cancel'
		);
		
		foreach ($text_strings as $text) {
			$data[$text] = $this->language->get($text);
		}
		$this->load->model('module/invoice_map');
		$ifi_invoice = $this->model_setting_setting->getSetting('ifi_invoice');
		$vat = array_key_exists(ModelModuleInvoiceMap::API_VAT, $ifi_invoice) && $ifi_invoice[ModelModuleInvoiceMap::API_VAT];
		$api_bill = array_key_exists(ModelModuleInvoiceMap::API_KEY_BILL, $ifi_invoice) ? $ifi_invoice[ModelModuleInvoiceMap::API_KEY_BILL] : '';
		$api_invoice = array_key_exists(ModelModuleInvoiceMap::API_KEY_INVOICE, $ifi_invoice) ? $ifi_invoice[ModelModuleInvoiceMap::API_KEY_INVOICE] : '';
		$api_subscriber = array_key_exists(ModelModuleInvoiceMap::API_KEY_SUBSCRIBER, $ifi_invoice) ? $ifi_invoice[ModelModuleInvoiceMap::API_KEY_SUBSCRIBER] : '';
		$api_login = array_key_exists(ModelModuleInvoiceMap::API_LOGIN, $ifi_invoice) ? $ifi_invoice[ModelModuleInvoiceMap::API_LOGIN] : '';
		$config_data = array(
			'form_action'		=> $this->url->link('module/ifi_invoice', 'token=' . $this->session->data['token'], 'SSL'),
			'submit_name'		=> ModelModuleInvoiceMap::SUBMIT_CONF_NAME,
			'api_vat_name'		=> ModelModuleInvoiceMap::API_VAT,
			'api_vat_checked'		=> ($vat ? "checked=\"checked\"" : ""),
			'api_bill_name'		=> ModelModuleInvoiceMap::API_KEY_BILL,
			'api_bill_value'	=> $api_bill,
			'api_invoice_name'	=> ModelModuleInvoiceMap::API_KEY_INVOICE,
			'api_invoice_value'	=> $api_invoice,
			'api_subscriber_name'   => ModelModuleInvoiceMap::API_KEY_SUBSCRIBER,
			'api_subscriber_value'  => $api_subscriber,
			'api_login_name'	=> ModelModuleInvoiceMap::API_LOGIN,
			'api_login_value'	=> $api_login
		);
		$this->model_setting_setting->getSetting('ifi_invoice');

		foreach ($config_data as $key => $value ) {
			$data[$key] = $value;
		}

		//This creates an error message. The error['warning'] variable is set by the call to function validate() in this controller (below)
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_module'),
			'href'      => $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('module/ifi_invoice', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => ' :: '
		);

		$data['cancel'] = $this->url->link('extension/module', 'token=' . $this->session->data['token'], 'SSL');

		if(version_compare(VERSION, '1.9.0.0', '>')) {
			$data['text_edit']=$this->language->get('heading_title');
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$this->response->setOutput($this->load->view('module/ifi_invoice_oc2.tpl', $data));
		} else {
			$this->data=$data;
			//Choose which template file will be used to display this request.
			$this->template = 'module/ifi_invoice.tpl';
			$this->children = array(
				'common/header',
				'common/footer',
			);

			//Send the output.
			$this->response->setOutput($this->render());
		}
	}

	public function install(){
		$this->load->model('module/invoice_map');
		$this->model_module_invoice_map->getInstallDBSql();
		$this->load->model('setting/setting');
		$ifi_invoice_params = array( 
																ModelModuleInvoiceMap::API_VAT => false,
																ModelModuleInvoiceMap::API_KEY_BILL => '',
																ModelModuleInvoiceMap::API_KEY_INVOICE => '',
																ModelModuleInvoiceMap::API_KEY_SUBSCRIBER => '',
																ModelModuleInvoiceMap::API_LOGIN => '',
																ModelModuleInvoiceMap::API_HASH => self::_generateHash(ModelModuleInvoiceMap::API_HASH_LENGTH));
		$this->model_setting_setting->editSetting('ifi_invoice', $ifi_invoice_params);
	}

	public function uninstall(){
		$this->load->model('module/invoice_map');
		$this->model_module_invoice_map->getUninstallDBSql();
	}

	public function sendInvoice(){
		$this->load->language('module/ifi_invoice');
		$this->load->model('setting/setting');
		$this->load->model('module/invoice_map');

		$id = $this->request->get['id'];
		$type = $this->request->get['type'];
		$hash = $this->request->get['h'];
		$ifi_invoice = $this->model_setting_setting->getSetting('ifi_invoice');
		$settings_hash = $ifi_invoice[ModelModuleInvoiceMap::API_HASH];
		if(!ApiManager::getInstance()->checkIfirmaHash($hash, $settings_hash)){
			if(version_compare(VERSION, '1.9.0.0', '>'))
				$this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL'));
			else
				$this->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL'));
		}
		$this->load->model('sale/order');
		$order = $this->model_sale_order->getOrder($id);
		$sendResult = ApiManager::getInstance()->sendInvoice($order, $type);
		
		if ($this->getSendResultMessage($sendResult) && $type == 'bill'){
			$this->session->data['success'] = $this->language->get('ifi_bill_text_success');
		}else if ($this->getSendResultMessage($sendResult) && $type != 'bill'){
			$this->session->data['success'] = $this->language->get('ifi_invoice_text_success');
		}else{
			$this->session->data['error_warning'] =  $sendResult->getMessage();
		}
		if(version_compare(VERSION, '1.9.0.0', '>'))
			$this->response->redirect($this->request->server['HTTP_REFERER']);
		else
			$this->redirect($this->request->server['HTTP_REFERER']);
	}

	public function getInvoice(){
		$this->load->model('setting/setting');
		$this->load->model('module/invoice_map');
		$id = $this->request->get['id'];
		$hash = $this->request->get['h'];
		$ifi_invoice = $this->model_setting_setting->getSetting('ifi_invoice');
		$settings_hash = $ifi_invoice[ModelModuleInvoiceMap::API_HASH];
		if(!ApiManager::getInstance()->checkIfirmaHash($hash, $settings_hash)){
			if(version_compare(VERSION, '1.9.0.0', '>'))
				$this->response->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL'));
			else
				$this->redirect($this->url->link('sale/order', 'token=' . $this->session->data['token'], 'SSL'));
		}
		$invoice = $this->model_module_invoice_map->get($id);

		header('Content-Type: application/pdf');
		header('Content-disposition: attachment; filename="'.ApiManager::getInstance()->getDocumentPdfName($invoice).'"');
		echo ApiManager::getInstance()->getDocumentAsPdf($invoice);
	}

	private function getSendResultMessage($sendResult){
		if($sendResult === null)
			return false;

		return ($sendResult->isOk() ? true : false);
	}
	
	/*
	 * 
	 * This function is called to ensure that the settings chosen by the admin user are allowed/valid.
	 * You can add checks in here of your own.
	 * 
	 */
	private function validate() {
		if (!$this->user->hasPermission('modify', 'module/ifi_invoice'))
			$this->error['warning'] = $this->language->get('error_permission');

		if (!$this->error)
			return TRUE;
		else
			return FALSE;
	}

	private function _generateHash($length){
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		$count = mb_strlen($chars);

		for ($i = 0, $result = ''; $i < $length; $i++) {
			$index = rand(0, $count - 1);
			$result .= mb_substr($chars, $index, 1);
		}

		return $result;
	}
}

?>