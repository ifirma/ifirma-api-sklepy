<?php

namespace ifirma;

require_once dirname(__FILE__) . '/IfirmaException.php';

/**
 * Description of AccountancyMonth
 *
 * @author bbojanowicz
 */
class AccountancyMonth {
	
	const MONTHS_IN_YEAR = 12;
	
	/**
	 *
	 * @var int
	 */
	private $_month;
	
	/**
	 *
	 * @var int
	 */
	private $_year;
	
	/**
	 * 
	 * @param int $month
	 * @param int $year
	 */
	public function __construct($month, $year){
		$this->_month = $month;
		$this->_year = $year;
	}
	
	/**
	 * 
	 * @param \ifirma\AccountancyMonth $m1
	 * @param \ifirma\AccountancyMonth $m2
	 * @return int	0  : equal
	 *				-1 : first less
	 *				1  : first greater
	 */
	public static function compare(AccountancyMonth $m1, AccountancyMonth $m2){
		$diff = self::diffInMonths($m1, $m2);
		
		if($diff < 0){
			return -1;
		} elseif ($diff > 0){
			return 1;
		} else {
			return 0;
		}
	}
	
	/**
	 * 
	 * @param \ifirma\AccountancyMonth $m1
	 * @param \ifirma\AccountancyMonth $m2
	 * @return int
	 */
	public static function diffInMonths(AccountancyMonth $m1, AccountancyMonth $m2){
		return (($m1->_year - $m2->_year) * self::MONTHS_IN_YEAR) + ($m1->_month - $m2->_month);
	}
	
	/**
	 * 
	 * @param \ifirma\AccountancyMonth $otherMonth
	 * @return bool
	 */
	public function equals(AccountancyMonth $otherMonth){
		return self::compare($this, $otherMonth) === 0;
	}
}

