<?php

namespace ifirma;

/**
 *
 * @author bbojanowicz
 */
interface FilterInterface {
	
	/**
	 * 
	 * @param mixed $value
	 * @return mixed
	 */
	public function filter($value);
}

?>
