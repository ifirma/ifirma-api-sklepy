<?php

/**
 * Description of InvoiceController
 *
 * @author platowski
 */
class PowerMedia_Ifirma_InvoiceController extends Mage_Adminhtml_Controller_Action {

	public function apiAction() {
		$config = $this->getConfig();
		$action = Mage::getModel('adminhtml/url')->getUrl('ifirma/invoice/api');
		$bool = $this->getRequest()->getParam('api_key_update');
		if (!empty($bool)) {
			$new_config = array(
				'API_KEY_FAKTURA' => $this->getRequest()->getParam('API_KEY_FAKTURA'),
				'API_KEY_ABONENT' => $this->getRequest()->getParam('API_KEY_ABONENT'),
				'API_LOGIN' => $this->getRequest()->getParam('API_LOGIN')
			);
			$this->saveConfig($new_config);
			$config=$new_config;
		}
		$this->loadLayout();
		$head = $this->getLayout()
				->createBlock('core/text', 'header')
				->setText('<h3>Księgowość internetowa ifirma.pl - konfiguracja API</h3>');
		$this->_addContent($head);

		$form_content = "<form action='" . $action . "' method='get'>
		<div class='formRow'>
			<label for='apiKey'>Klucz do API - faktura:</label><input id='apiKey' type='text' name='API_KEY_FAKTURA' value=" . $config['API_KEY_FAKTURA'] . " class='text'/>
		</div>
		<div class='formRow'>
			<label for='apiKey'>Klucz do API - abonent:</label><input id='apiKey' type='text' name='API_KEY_ABONENT' value=" . $config['API_KEY_ABONENT'] . " class='text'/>
		</div>
		<div class='formRow'>

			<label for='apiLogin'>Login do API:</label><input id='apiLogin' type='text' name='API_LOGIN' value=" . $config['API_LOGIN'] . " class='text'/>
		</div>
		
		<div class='formRow'>
			<input type='submit' value='Zapisz parametry' name='api_key_update' />
			
		</div>
	</form>";

		$form = $this->getLayout()
				->createBlock('core/text', 'form')
				->setText($form_content);
		$this->_addContent($form);

		$this->renderLayout();
	}

	public function invoiceAction() {
		$order_id = $this->getRequest()->getParam('order_id');
		/* @var $order Mage_Sales_Model_Order */
		$order = Mage::getModel('sales/order')->load($order_id);
		if (!$this->canProcessInvoice($order)) {
			throw new Zend_Exception('Użytkownik nie może wystawić faktury do tego zamówienia.');
		}
		$wysylka = false;
		if ($this->getRequest()->getParam('type') == 'send') {
			$wysylka = true;
		}
		if ($wysylka == false) {
			$invoice = new PowerMedia_Ifirma_Model_Invoice();
			$type = 'invoice';
		} else {
			$invoice = new PowerMedia_Ifirma_Model_InvoiceSend();
			$type = 'invoice-send';
		}
		$invoice->init($order);
		/* Check Accountancy Month */

		$creation_date = $invoice->getDataWystawienia();
		$config = $this->getConfig();
		try {
			$this->setAccountancyMonth(substr($creation_date, 5, 2), substr($creation_date, 0, 4), $config);
			$response = $this->sendInvoiceCreationRequest($invoice, $config);
			
			if ($response['response']['Kod'] != 0) {
				Mage::getSingleton('core/session')->addError('Nie udało się wystawić faktury!');
				$this->_redirectReferer();
			}
		} catch (Zend_Exception $e) {
			Mage::getSingleton('core/session')->addError('Nie udało się wystawić faktury!');
			$this->_redirectReferer();
		}
		/* Store Invoice info */
		try {
			$this->saveDocumentDataFromResponse($response, $type, $order_id);
			//$this->verifyInvoice($invoice, $order);
		} catch (Zend_Exception $e) {
			Mage::getSingleton('core/session')->addError('Wystąpił błąd: '.$e->getMessage());
			$this->_redirectReferer();
		}
		if ($response['response']['Kod'] == 0) {
			Mage::getSingleton('core/session')->addSuccess('Faktura dodana pomyślnie!');
		}
		$this->_redirectReferer();
	}

	public function downloadAction() {
		$order_id = $this->getRequest()->getParam('order_id');
		Mage::getModel('ifirma/ifirma')->getResource()->getReadConnection()->select();
		$document = $this->getInvoiceIfAvailable($order_id);
		$response = $this->sendDocumentDownloadRequest($document, $this->getConfig());

		header('Content-Type: application/pdf');
		header('Content-Disposition: attachment; filename="invoice' . '.pdf"');
		echo $response;
	}

	public function verifiedAction() {
		$order_id = $this->getRequest()->getParam('order_id');
		Mage::getModel('ifirma/ifirma')->getResource()->getReadConnection()->select();
		$document = $this->getInvoiceIfAvailable($order_id);
		$this->addInfoAboutInvoiceVerified($document);
		Mage::getSingleton('core/session')->addSuccess('Faktura zweryfikowana pomyślnie!');
		$this->_redirectReferer();
	}

	private function getConfig() {
		$API_CONFIG_FILE_PATH = getcwd() . '/app/code/local/PowerMedia/Ifirma/config.ini';
		$config = parse_ini_file($API_CONFIG_FILE_PATH);
		return $config;
	}

	private function saveConfig(array $config) {
		$API_CONFIG_FILE_PATH = getcwd() . '/app/code/local/PowerMedia/Ifirma/config.ini';
		$this->write_php_ini($API_CONFIG_FILE_PATH, $config);
	}

	private function write_php_ini($fileName, $array) {
		$res = array();
		foreach ($array as $key => $val) {
			if (is_array($val)) {
				$res[] = "[$key]";
				foreach ($val as $skey => $sval) {
					$res[] = "$skey = " . (is_numeric($sval) ? $sval : '"' . $sval . '"');
				}
			} else {
				$res[] = "$key = " . (is_numeric($val) ? $val : '"' . $val . '"');
			}
		}
		$this->safefilerewrite($fileName, implode("\r\n", $res));
	}

	private function safefilerewrite($fileName, $dataToSave) {
		if ($fp = fopen($fileName, 'w')) {
			$startTime = microtime();
			do {
				$canWrite = flock($fp, LOCK_EX);
				// If lock not obtained sleep for 0 - 100 milliseconds, to avoid collision and CPU load
				if (!$canWrite) {
					usleep(round(rand(0, 100) * 1000));
				}
			} while ((!$canWrite) and ((microtime() - $startTime) < 1000));
			if ($canWrite) {
				fwrite($fp, $dataToSave);
				flock($fp, LOCK_UN);
			}
			fclose($fp);
		}
	}

	private function canProcessInvoice(Mage_Sales_Model_Order $order) {
		$results = $this->getInvoiceIfAvailable($order->getId());
		if ($results === false || $results == null) {
			return true;
		}
		return false;
	}

	private function getInvoiceIfAvailable($orderId) {
		$select = Mage::getModel('ifirma/ifirma')->getResource()->getReadConnection()->select();
		 $resource = Mage::getSingleton('core/resource');
                $tableName = $resource->getTableName('ifirma/ifirma');

		$select->from($tableName);
		$select->where('order_id = ?', $orderId);
		$data = Mage::getModel('ifirma/ifirma')->getResource()->getReadConnection()->fetchAll($select);
		if (empty($data)) {
			return null;
		}
		$document = Mage::getModel('ifirma/ifirma')->load($data[0]["id"]);
		return $document;
	}

	private function verifyInvoice($invoice, $order) {
		$faktura_sum = 0;
		$generated_invoice_data = $this->getInvoiceIfAvailable($order->getId());
		$generated_invoice = $this->sendDocumentDownloadRequest($generated_invoice_data, $this->getConfig(), 'json');
		$generated_invoice = $this->_decodeJson($generated_invoice);
		$generated_invoice = $generated_invoice['response'];
		$invpos = $generated_invoice["Pozycje"];
		$positions = $invoice->getPozycje();
		$l = 0;
		$error = true;
		foreach ($positions as $pos) {
			$vatDwa = (float) $invpos[$l]['StawkaVat'];
			$cenaDwa = (float) $invpos[$l]['CenaJednostkowa'];
			$vatRaz = (float) $pos['StawkaVat'];
			$cenaRaz = (float) $pos['CenaJednostkowa'];

			if ($vatRaz != $vatDwa || $cenaRaz != $cenaDwa)
				$error = true;
			$l++;
		}
		if ($error == true) {
			$this->addInfoAboutInvoiceFault($generated_invoice_data);
		}
	}

	private function addInfoAboutInvoiceFault($generated_invoice_data) {
		$generated_invoice_data->setData('correction_needed', 1);
		$generated_invoice_data->save();
	}

	private function addInfoAboutInvoiceVerified($generated_invoice_data) {
		$generated_invoice_data->setData('correction_done', 1);
		$generated_invoice_data->save();
	}

	private function setAccountancyMonth($month, $year, $config) {
		try {
			$month_array = $this->fetchAccountancyMonth($config);
		} catch (Zend_Exception $e) {
			throw new Zend_Exception($e->getMessage());
		}
		$given = $year . $month;
		$fetched = $month_array['RokKsiegowy'] . str_pad($month_array['MiesiacKsiegowy'], 2, "0", STR_PAD_LEFT);
		$compare_result = strcmp($given, $fetched);
		if ($compare_result == 0)
			return;
		if ($compare_result < 0) {
			$request = new PowerMedia_Ifirma_Model_Month(true, false);
		} else {
			$request = new PowerMedia_Ifirma_Model_Month(true, true);
		}
		$this->changeAccountancyMonthRequest($request, $config);
		$this->setAccountancyMonth($month, $year, $config);
	}

	/**
	 *
	 * @param type $request
	 * @param type $rental_agency
	 * @return type 
	 */
	public function changeAccountancyMonthRequest($request, $connector) {
		$userName = $connector['API_LOGIN'];
		$key_hex = $connector['API_KEY_ABONENT'];
		$key = PowerMedia_Ifirma_Model_IFirmaApi::hexToStr($key_hex);
		$keyName = PowerMedia_Ifirma_Model_IFirmaApi::SUBSCRIBER_KEY_NAME;

		$messageHash = PowerMedia_Ifirma_Model_IFirmaApi::hmac($key, PowerMedia_Ifirma_Model_IFirmaApi::API_ACCOUNTANCY_MONTH_URL . $userName . $keyName . $request->toJson());
		$client = $this->setHttpInvoiceClient($userName, $messageHash, $request);
		$response = $this->sendSafeRequestFromHttpClient($client, 'PUT');
		$response_array = $this->_decodeJson($response->getBody());
		return $response_array['response'];
	}

	public function fetchAccountancyMonth($connector) {
		$request = new PowerMedia_Ifirma_Model_Month();
		$userName = $connector['API_LOGIN'];
		$key_hex = $connector['API_KEY_ABONENT'];
		$key = PowerMedia_Ifirma_Model_IFirmaApi::hexToStr($key_hex);
		$keyName = PowerMedia_Ifirma_Model_IFirmaApi::SUBSCRIBER_KEY_NAME;

		$messageHash = PowerMedia_Ifirma_Model_IFirmaApi::hmac($key, PowerMedia_Ifirma_Model_IFirmaApi::API_ACCOUNTANCY_MONTH_URL . $userName . $keyName);
		$client = $this->setHttpInvoiceClient($userName, $messageHash, $request);

		$response = $this->sendSafeRequestFromHttpClient($client, 'GET');
		$response_array = $this->_decodeJson($response->getBody());

//		Zend_Debug::dump($response_array);
		return $response_array['response'];
	}

	public function sendInvoiceCreationRequest($invoice, $connector) {
		$userName = $connector['API_LOGIN'];
		$key_hex = $connector['API_KEY_FAKTURA'];
		$key = PowerMedia_Ifirma_Model_IFirmaApi::hexToStr($key_hex);
		$keyName = PowerMedia_Ifirma_Model_IFirmaApi::INVOICE_KEY_NAME;
		$messageHash = PowerMedia_Ifirma_Model_IFirmaApi::hmac($key, $this->_determineUriBasedOnRequestClass($invoice) . $userName . $keyName . $invoice->toJson());

		$client = $this->setHttpInvoiceClient($userName, $messageHash, $invoice);

		$response = $this->sendSafeRequestFromHttpClient($client, 'POST');

		$response_obj = $this->_decodeJson($response->getBody());
//Zend_Debug::dump($invoice);
//		Zend_Debug::dump($response->getBody());
//		echo $response->__toString();
//		Zend_Debug::dump(Zend_Json::encode($response_obj));
		return $response_obj;
	}

	public function sendDocumentDownloadRequest($invoice, $connector, $format = 'pdf') {
		$userName = $connector['API_LOGIN'];
		$key_hex = $connector['API_KEY_FAKTURA'];
		$key = PowerMedia_Ifirma_Model_IFirmaApi::hexToStr($key_hex);
		$keyName = PowerMedia_Ifirma_Model_IFirmaApi::INVOICE_KEY_NAME;
		$messageHash = PowerMedia_Ifirma_Model_IFirmaApi::hmac($key, $this->_determineUriBasedOnRequestClass($invoice, $format) . $userName . $keyName);

		$client = $this->setHttpInvoiceClient($userName, $messageHash, $invoice, $format);
		$response = $this->sendSafeRequestFromHttpClient($client, 'GET');
		return $response->getBody();
	}

	/**
	 *
	 * @param type $userName
	 * @param type $messageHash
	 * @param type $request
	 * @return Zend_Http_Client 
	 */
	public function setHttpInvoiceClient($userName, $messageHash, $request, $format='pdf') {
		/* Zend_Http_Client creation */
		$client = new Zend_Http_Client();
		$client->setUri($this->_determineUriBasedOnRequestClass($request, $format));
		$client->setConfig(array(
			'maxredirects' => 0,
			'timeout' => 20,
		));
		$client->setHeaders(array(
			'Accept: application/json',
			'Content-type: application/json; charset=UTF-8',
			'Authentication: IAPIS user=' . $userName . ', hmac-sha1=' . $messageHash
		));
		if ($this->_shouldRawDataBeSendForRequest($request)) {
			$client->setRawData($request->toJson(), 'text/json');
		}
		return $client;
	}

	public function sendSafeRequestFromHttpClient(Zend_Http_Client $client, $method = 'POST') {
		try {
			$response = $client->request($method);
		} catch (Zend_Http_Client_Exception $e) {
			throw new Zend_Exception('Unable to connect with ifirma.pl. Please try again later.');
		}
		return $response;
	}

	public function _determineUriBasedOnRequestClass($invoice, $format='pdf') {
		if ($invoice instanceof PowerMedia_Ifirma_Model_Invoice) {
			return PowerMedia_Ifirma_Model_IFirmaApi::API_INVOICE_URL;
		}
		if ($invoice instanceof PowerMedia_Ifirma_Model_InvoiceSend) {
			return PowerMedia_Ifirma_Model_IFirmaApi::API_INVOICE_SEND_URL;
		}
		if ($invoice instanceof PowerMedia_Ifirma_Model_Month) {
			return PowerMedia_Ifirma_Model_IFirmaApi::API_ACCOUNTANCY_MONTH_URL;
		}
		if ($invoice instanceof PowerMedia_Ifirma_Model_Ifirma) {
			return PowerMedia_Ifirma_Model_IFirmaApi::determineDocumentDownloadURL($invoice, $format);
		}
	}

	/**
	 *
	 * @param string $json
	 * @return mixed
	 */
	private function _decodeJson($json) {
		try {
			$output = Zend_Json::decode($json);
		} catch (Zend_Json_Exception $e) {
			throw new Zend_Json_Exception('Unable to connect with ifirma.pl. Please try again later.');
		}
		return $output;
	}

	private function _shouldRawDataBeSendForRequest($request) {

		if ($request instanceof PowerMedia_Ifirma_Model_Month && !$request->isForChange()) {
			return false;
		}
		if ($request instanceof PowerMedia_Ifirma_Model_AccountancyDocumentData) {
			return false;
		}
		return true;
	}

	public function saveDocumentDataFromResponse($response, $type, $order_id) {
		$response = $response['response'];
		$row = new PowerMedia_Ifirma_Model_Ifirma();
		$arr = array(
			'invoice_type' => $type,
			'document_type' => 'invoice',
			'invoice_number' => $response['Identyfikator'],
			'order_id' => $order_id
		);
		if(empty($response['Identyfikator'])){
			throw new Zend_Exception($response['Informacja']);
                }
		$row->addData($arr);
		try {
			$row->save();
		} catch (Zend_Db_Exception $e) {
			throw new Zend_Exception('Błąd zapisu danych dokumentu'.$e->getMessage());
		}
	}

}
