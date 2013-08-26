<?php

/**
 * Description of Month
 *
 * @author platowski
 */
class PowerMedia_Ifirma_Model_Month {

	private $is_next;
	private $is_for_change;
	public function __construct($is_for_change = false, $is_next=false) {
		$this->is_next = $is_next;
		$this->is_for_change = $is_for_change;
	}
	public function isForChange(){
		return $this->is_for_change;
	}
	/**
	 *
	 * @return string Json array
	 */
	public function toJson() {
		if ($this->is_next) {
			return '{"MiesiacKsiegowy":"NAST","PrzeniesDaneZPoprzedniegoRoku":true}';
		} else {
			return '{"MiesiacKsiegowy":"POPRZ","PrzeniesDaneZPoprzedniegoRoku":true}';
		}
	}

}