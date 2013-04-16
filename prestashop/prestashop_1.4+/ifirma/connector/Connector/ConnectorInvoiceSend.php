<?php

namespace ifirma;

require_once dirname(__FILE__) . '/ConnectorAbstract.php';

/**
 * Description of ConnectorInvoiceSend
 *
 * @author bbojanowicz
 */
class ConnectorInvoiceSend extends ConnectorAbstract{
	
	/**
	 * @return Response
	 */
	public function send(){
		$this->_setProperAccountancyMonth();
		
		$content = $this->_invoice->toJson();
		$this->_initCurrentCurl(self::API_INVOICE_SEND_URL);
		$key = Utils::hexToStr($this->_getConfig()->{Config::API_KEY_INVOICE});
		$messageHash = Utils::hmac($key, sprintf(
			"%s%s%s%s",
			self::API_INVOICE_SEND_URL,
			$this->_getConfig()->{Config::API_LOGIN},
			Config::API_KEY_INVOICE_NAME,
			$content
		));
		
		$this->_setCurlPostOptions(
			self::API_INVOICE_SEND_URL,
			$this->_getRequestHeaders($messageHash), 
			$content
		);
		
		return Response::factory(curl_exec($this->_currentCurl));
	}
	
	/**
	 * 
	 * @param int $id
	 * @return \ifirma\ConnectorInvoiceSend
	 */
	public function receive($id){
		$receiveUrl = sprintf(
				"%s%d.%s",
				self::API_GET_INVOICE_SEND_URL,
				intval($id),
				self::DEFAULT_CONNECTION_FILE_TYPE
		);
		$this->_initCurrentCurl($receiveUrl);
		
		$key = Utils::hexToStr($this->_getConfig()->{Config::API_KEY_INVOICE});
		$messageHash = Utils::hmac($key, sprintf(
			"%s%s%s",
			$receiveUrl,
			$this->_getConfig()->{Config::API_LOGIN},
			Config::API_KEY_INVOICE_NAME
		));
		
		$this->_setCurlGetOptions($receiveUrl, $this->_getRequestHeaders($messageHash));
		
		$rsp = curl_exec($this->_currentCurl);
		$this->_populateInvoice($rsp);
		
		return $this;
	}
	
	/**
	 * @return binary
	 */
	public function receivePdf(){
		$receiveUrl = sprintf(
			"%s%s.%s.%s",
			self::API_GET_INVOICE_SEND_URL,
			InvoiceAbstract::filterNumber($this->_invoice->{InvoiceResponse::KEY_PELNY_NUMER}),
			self::FILE_TYPE_PDF,
			self::INVOICE_TYPE_ORIGINAL
		); 
		$this->_initCurrentCurl($receiveUrl);
			
		$key = Utils::hexToStr($this->_getConfig()->{Config::API_KEY_INVOICE});
		$messageHash = Utils::hmac($key, sprintf(
			"%s%s%s",
			$receiveUrl,
			$this->_getConfig()->{Config::API_LOGIN},
			Config::API_KEY_INVOICE_NAME
		));
		
		$this->_setCurlGetOptions($receiveUrl, $this->_getRequestHeaders($messageHash, self::FILE_TYPE_PDF));
		return curl_exec($this->_currentCurl);
	}
}

