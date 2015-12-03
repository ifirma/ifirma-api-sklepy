<?php
namespace ifirma;
/**
 * @author kkolcz
 */
class Vat{
	
	const VAT_23 = 0.23;/*current*/
	const VAT_22 = 0.22;
	const VAT_8 = 0.8;/*current*/
	const VAT_7 = 0.7;
	const VAT_5 = 0.5;/*current*/
	const VAT_3 = 0.3;

    public function currentVat($vat){
        if(number_format(self::VAT_3,2) == number_format($vat,2)){
            return number_format(self::VAT_5,2);
        }   

        if(number_format(self::VAT_7,2) == number_format($vat,2)){
            return number_format(self::VAT_8,2);
        }   

        if(number_format(self::VAT_22,2) == number_format($vat,2)){
            return number_format(self::VAT_23,2);
        }   

        return $vat;
    }

	/**
	 *
	 * @var Vat 
	 */
	private static $_instance;
	
	private function __construct(){
	}

	/**
	 * 
	 * @return Vat 
	 */
	public static function getInstance(){
		if(self::$_instance === null){
			self::$_instance = new self();
		}
		return self::$_instance;
	}
	
}
