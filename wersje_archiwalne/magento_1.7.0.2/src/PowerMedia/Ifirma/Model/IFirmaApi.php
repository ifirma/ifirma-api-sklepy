<?php


/**
 *
 * @author platowski
 */
class PowerMedia_Ifirma_Model_IFirmaApi{

	const API_INVOICE_URL = 'https://www.ifirma.pl/iapi/fakturakraj.json';
	const API_INVOICE_SEND_URL = 'https://www.ifirma.pl/iapi/fakturawysylka.json';
	const API_ADVANCE_INVOICE_URL = 'https://www.ifirma.pl/iapi/fakturazaliczka.json';
	const API_PROFORMA_URL = 'https://www.ifirma.pl/iapi/fakturaproformakraj.json';
	const API_BILL_URL = 'https://www.ifirma.pl/iapi/rachunekkraj.json';
	const API_ACCOUNTANCY_MONTH_URL = 'https://www.ifirma.pl/iapi/abonent/miesiacksiegowy.json';
	const API_REGISTER_USER_URL = 'https://www.ifirma.pl/iapi/abonent/rejestracja.json';
	const INVOICE_KEY_NAME = 'faktura';
	const SUBSCRIBER_KEY_NAME = 'abonent';
	const BILL_KEY_NAME = 'rachunek';

	public static function hmac($key, $data) {
		$blocksize = 64;
		$hashfunc = 'sha1';
		if (strlen($key) > $blocksize)
			$key = pack('H*', $hashfunc($key));
		$key = str_pad($key, $blocksize, chr(0x00));
		$ipad = str_repeat(chr(0x36), $blocksize);
		$opad = str_repeat(chr(0x5c), $blocksize);
		$hmac = pack('H*', $hashfunc(($key ^ $opad) . pack('H*', $hashfunc(($key ^ $ipad) . $data))));
		return bin2hex($hmac);
	}

	public static function hexToStr($hex) {
		$string = '';
		for ($i = 0; $i < strlen($hex) - 1; $i+=2) {
			$string .= chr(hexdec($hex[$i] . $hex[$i + 1]));
		}
		return $string;
	}

	/*
	 * 
	 */

	public static function determineKeyNameBasedOnDocumentType($type) {
		if ($type == 'invoice' || $type == 'invoice-advance' || $type == 'invoice-final') {
			return self::INVOICE_KEY_NAME;
		}
	}

	public static function determineDocumentDownloadURL($document,$format) {
		$documentd = $document->getData();
		switch ($documentd['invoice_type']) {
			case 'invoice':
				return 'https://www.ifirma.pl/iapi/fakturakraj/' . $documentd['invoice_number'] . '.'.$format;
				break;
			case 'invoice-send' :
				return 'https://www.ifirma.pl/iapi/fakturawysylka/' . $documentd['invoice_number'] . '.'.$format;
				break;
		}
	}
	public static function checkIfInvoiceNeedsAttention($order_id){
		$invoice = self::getInvoiceIfAvailable($order_id);
		if($invoice === null){
			return false;
		}
		if($invoice->getData('correction_needed')=='1' && $invoice->getData('correction_done')=='0'){
			return true;
		}
		return false;
	}
	public static function getInvoiceIfAvailable($order_id){
		$select = Mage::getModel('ifirma/ifirma')->getResource()->getReadConnection()->select();
                $resource = Mage::getSingleton('core/resource');
                $tableName = $resource->getTableName('ifirma/ifirma');

		$select->from($tableName);
		$select->where('order_id = ?', $order_id);

		$data = Mage::getModel('ifirma/ifirma')->getResource()->getReadConnection()->fetchAll($select);

		if(empty($data)){
			return null;
		}
		$document = Mage::getModel('ifirma/ifirma')->load($data[0]["id"]);
		return $document;
	}

}