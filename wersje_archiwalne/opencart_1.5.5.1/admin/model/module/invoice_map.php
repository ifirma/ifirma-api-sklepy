<?php

require_once dirname(__FILE__) . '/invoice_map_collection.php';
require_once dirname(__FILE__) . '/../../../connector/DataContainer.php';
require_once dirname(__FILE__) . '/../../../connector/Filters/InvoiceNumberFilter.php';
require_once(dirname(__FILE__) . '/../../../system/library/customer.php');

foreach (glob(dirname(__FILE__).'/../../../catalog/model/shipping/*.php') as $filename) {
    $path = $filename;
    if (is_file($path)) {
        require $path;
    }
}
class ModelModuleInvoiceMap extends Model{
	
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
        
        const API_VAT = 'ifirma_api_vat';
	const API_KEY_BILL = 'ifirma_api_key_bill';
	const API_KEY_INVOICE = 'ifirma_api_key_invoice';
	const API_KEY_SUBSCRIBER = 'ifirma_api_key_subscriber';
	const API_LOGIN = 'ifirma_api_login';
	const API_HASH = 'ifirma_hash';
	const API_HASH_LENGTH = 32;
	
	const SUBMIT_CONF_NAME = 'submitIfirmaSettings';
	
	const KEY_ORDER_ID = 'id_order';
	
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
	public function getName($name){		
                return constant($name);
	}
	
	/**
	 * @return string
	 */
	public function getFilteredNumber($invoiceMap){
		$filter = new InvoiceNumberFilter();
		
		return $filter->filter($invoiceMap[self::COLUMN_NAME_INVOICE_NUMBER]);
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
	 * @return InvoiceMapCollection
	 */
	public function getInvoiceMapRowsForOrderId($orderId){
                $query = sprintf(
			"SELECT %s FROM %s WHERE %s = %d",
			implode(', ', self::getColumnNames()),
                        DB_PREFIX.self::TABLE_NAME,
                        self::COLUMN_NAME_ORDER_ID,
                        intval($orderId)
		);
		return new InvoiceMapCollection($this->db->query($query)->rows);
	}
	
	/**
	 * 
	 * @param int $id
	 * @return row|null
	 */
	public function get($id){
		if(!isset(self::$_invoiceMaps[$id])){
			self::$_invoiceMaps[$id] = $this->_get($id);
		}
		
		return self::$_invoiceMaps[$id];
	}
	

	/**
	 * 
	 * @param int $id
	 * @return row|null
	 */
	private function _get($id){
                $query = sprintf(
			"SELECT %s FROM %s WHERE %s = %d",
			implode(', ', self::getColumnNames()),
                        DB_PREFIX.self::TABLE_NAME,
                        self::COLUMN_NAME_ID,
                        intval($id)
		);
                $res = $this->db->query($query)->rows;
                
		if(count($res) !== 1){
			return null;
		}

		return $res[0];
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
	
	public function getInstallDBSql(){
		$query = sprintf(
			"CREATE TABLE IF NOT EXISTS `%s%s` (
				`%s` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`%s` INT(11) NOT NULL,
				`%s` INT(11) NOT NULL,
				`%s` VARCHAR(32),
				`%s` ENUM('%s') NOT NULL
			)DEFAULT CHARSET=utf8;",
			DB_PREFIX,
			self::TABLE_NAME,
			self::COLUMN_NAME_ID,	
			self::COLUMN_NAME_ORDER_ID,
			self::COLUMN_NAME_INVOICE_ID,
			self::COLUMN_NAME_INVOICE_NUMBER,
			self::COLUMN_NAME_INVOICE_TYPE,
			implode('\', \'', self::getInvoiceTypes())
		);
                return $this->db->query($query);
	}
	
	public function getUninstallDBSql(){
		$query = sprintf(
			"DROP TABLE IF EXISTS `%s%s`;",
			DB_PREFIX,
			self::TABLE_NAME
		);
                return $this->db->query($query);                
	}
        
	public function save(){                
                $query = ("INSERT INTO `" . DB_PREFIX . self::TABLE_NAME
                        ."` SET ".self::COLUMN_NAME_INVOICE_ID." = '".(int)$this->{self::COLUMN_NAME_INVOICE_ID}
                        . "', ".self::COLUMN_NAME_INVOICE_NUMBER." = '" . $this->{self::COLUMN_NAME_INVOICE_NUMBER} 
                        . "', ".self::COLUMN_NAME_INVOICE_TYPE." = '" . $this->{self::COLUMN_NAME_INVOICE_TYPE}
                        . "', ".self::COLUMN_NAME_ORDER_ID." = '" . (int)$this->{self::COLUMN_NAME_ORDER_ID} . "'");
		
		return $this->db->query($query);
	}
	
	public function updateInvoiceNumber($invoice, $newInvoiceNumber){
		return $this->db->query("UPDATE " . DB_PREFIX . self::TABLE_NAME. " SET ".self::COLUMN_NAME_INVOICE_NUMBER." = '" . $newInvoiceNumber . "' WHERE ".self::COLUMN_NAME_ID." = '" . (int)$invoice . "'");
	}
        
        public function getOrderShippingCost($order_id) {
         	return $this->db->query("SELECT * FROM `" . DB_PREFIX . "order_total` WHERE order_id = '" . (int)$order_id . "' AND code='shipping'")->row;
	}
        
        public function getShippingTax($shippingCode, $countryId, $zoneId, $type){
            $shipping_array = explode('.', $shippingCode);
	    $code = $shipping_array[0];		
            $shippingModel = 'ModelShipping'.ucfirst($code);
            $this->load->library('tax');
            $this->registry->set('customer', new Customer($this->registry));
            $shippingModel = new $shippingModel( $this->registry );
            $this->tax = new Tax( $this->registry );
            if ($this->config->get($code . '_status')) {
                    $quote =  $shippingModel->getQuote(array('country_id' => $countryId, 'zone_id' => $zoneId)); 
                    if ($quote) {
                        return $this->tax->getTaxValue($quote['quote'][$quote['code']]['tax_class_id'], $type);
                    }  else {
                        return false;
                    }
            }
        }
        
        public function getProductTax($productId, $type){
            $this->load->model('catalog/product');
            $product = $this->model_catalog_product->getProduct($productId);
            $this->load->library('tax');
            $this->tax = new Tax( $this->registry );
            return $this->tax->getTaxValue($product['tax_class_id'], $type);
        }
        
        public function isModuleInstalled(){
            $result = $this->db->query("SELECT * FROM `" . DB_PREFIX . "extension` WHERE `code` = 'ifi_invoice'");
            if($result->num_rows) {
                return true;
            } else {
                return false;
            }
        }
}
