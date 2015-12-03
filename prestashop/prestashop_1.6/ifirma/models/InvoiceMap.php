<?php

namespace ifirma; 

require_once dirname(__FILE__) . '/InvoiceMapCollection.php';
require_once dirname(__FILE__) . '/../connector/DataContainer.php';
require_once dirname(__FILE__) . '/../connector/Filters/InvoiceNumberFilter.php';

/**
 * Description of IfirmaInvoiceMap
 *
 * @author bbojanowicz
 */
class InvoiceMap extends DataContainer{
	
	const TABLE_NAME = 'ifirma_invoice_map';
	
	const COLUMN_NAME_ID = 'id';
	const COLUMN_NAME_ORDER_ID = 'order_id';
	const COLUMN_NAME_INVOICE_ID = 'invoice_id';
	const COLUMN_NAME_INVOICE_NUMBER = 'invoice_number';
	const COLUMN_NAME_INVOICE_TYPE = 'invoice_type';
	
	const INVOICE_TYPE_NORMAL = 'invoice';
	const INVOICE_TYPE_SEND = 'invoice_send';
	const INVOICE_TYPE_PROFORMA = 'invoice_proforma';
	const INVOICE_TYPE_BILL = 'invoice_bill';
	
	/**
	 *
	 * @var array
	 */
	private static $_invoiceMaps = array();
	
	/**
	 * 
	 * @return array
	 */
	public static function getColumnNames(){
		return array(
			self::COLUMN_NAME_ID,
			self::COLUMN_NAME_INVOICE_ID,
			self::COLUMN_NAME_ORDER_ID,
			self::COLUMN_NAME_INVOICE_NUMBER,
			self::COLUMN_NAME_INVOICE_TYPE,
		);
	}
	
	/**
	 * @return string
	 */
	public function getFilteredNumber(){
		$filter = new InvoiceNumberFilter();
		
		return $filter->filter($this->{self::COLUMN_NAME_INVOICE_NUMBER});
	}
	
	/**
	 * 
	 * @return array
	 */
	public function getSupportedKeys(){
		return self::getColumnNames();
	}
	
	/**
	 * 
	 * @param int $orderId
	 * @return \ifirma\InvoiceMapCollection
	 */
	public static function getInvoiceMapRowsForOrderId($orderId){
		$sql = new \DbQuery();
		$sql->select(implode(', ', self::getColumnNames()));
		$sql->from(self::TABLE_NAME);
		$sql->where(sprintf("%s = %d", self::COLUMN_NAME_ORDER_ID, intval($orderId)));
		
		return new InvoiceMapCollection(\Db::getInstance()->executeS($sql));
	}
	
	/**
	 * 
	 * @param int $id
	 * @return InvoiceMap|null
	 */
	public static function get($id){
		if(!isset(self::$_invoiceMaps[$id])){
			self::$_invoiceMaps[$id] = self::_get($id);
		}
		
		return self::$_invoiceMaps[$id];
	}
	
	/**
	 * 
	 * @param int $id
	 * @return InvoiceMap|null
	 */
	private static function _get($id){
		$sql = new \DbQuery();
		$sql->select(implode(', ', self::getColumnNames()));
		$sql->from(self::TABLE_NAME);
		$sql->where('id = '.intval($id));
		
		$res = \Db::getInstance()->executeS($sql);
		if(count($res) !== 1){
			return null;
		}
		
		$invoiceMap = new InvoiceMap();
		foreach($res[0] as $key => $value){
			$invoiceMap->$key = $value;
		}
		
		return $invoiceMap;
	}
	
	/**
	 * 
	 * @return array
	 */
	public static function getInvoiceTypes(){
		return array(
			self::INVOICE_TYPE_BILL,
			self::INVOICE_TYPE_NORMAL,
			self::INVOICE_TYPE_PROFORMA,
			self::INVOICE_TYPE_SEND
		);
	}
	
	/**
	 * @return string
	 */
	public static function getInstallDBSql(){
		return sprintf(
			"CREATE TABLE IF NOT EXISTS `%s%s` (
				`%s` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`%s` INT(11) NOT NULL,
				`%s` INT(11) NOT NULL,
				`%s` VARCHAR(32),
				`%s` ENUM('%s') NOT NULL
			) ENGINE=%s DEFAULT CHARSET=utf8;",
			_DB_PREFIX_,
			self::TABLE_NAME,
			self::COLUMN_NAME_ID,	
			self::COLUMN_NAME_ORDER_ID,
			self::COLUMN_NAME_INVOICE_ID,
			self::COLUMN_NAME_INVOICE_NUMBER,
			self::COLUMN_NAME_INVOICE_TYPE,
			implode('\', \'', self::getInvoiceTypes()),
			_MYSQL_ENGINE_
		);
	}
	
	/**
	 * @return string
	 */
	public static function getUninstallDBSql(){
		return sprintf(
			"DROP TABLE IF EXISTS `%s%s`;",
			_DB_PREFIX_,
			self::TABLE_NAME
		);
	}
	
	/**
	 * 
	 * @return \ifirma\InvoiceMap
	 */
	public function save(){
		\Db::getInstance()->insert(self::TABLE_NAME, array(
			self::COLUMN_NAME_ID					=> (int)$this->{self::COLUMN_NAME_ID},
			self::COLUMN_NAME_INVOICE_ID			=> (int)$this->{self::COLUMN_NAME_INVOICE_ID},
			self::COLUMN_NAME_INVOICE_NUMBER		=> pSQL($this->{self::COLUMN_NAME_INVOICE_NUMBER}),
			self::COLUMN_NAME_INVOICE_TYPE		=> pSQL($this->{self::COLUMN_NAME_INVOICE_TYPE}),
			self::COLUMN_NAME_ORDER_ID			=> (int)$this->{self::COLUMN_NAME_ORDER_ID},
		));
		
		if($this->{self::COLUMN_NAME_ID} === null){
			$this->{self::COLUMN_NAME_ID} = \Db::getInstance()->Insert_ID();
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $newInvoiceNumber
	 * @return \ifirma\InvoiceMap
	 */
	public function updateInvoiceNumber($newInvoiceNumber){
		$this->{self::COLUMN_NAME_INVOICE_NUMBER} = $newInvoiceNumber;
		
		\Db::getInstance()->update(self::TABLE_NAME, array(
			self::COLUMN_NAME_INVOICE_NUMBER => pSQL($this->{self::COLUMN_NAME_INVOICE_NUMBER})
		), sprintf("%s = %d", self::COLUMN_NAME_ID, (int)$this->{self::COLUMN_NAME_ID}), 1 /* only one row */);
		
		return $this;
	}
}

