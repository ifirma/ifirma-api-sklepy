<?php

namespace ifirma;

require_once dirname(__FILE__) . '/IfirmaException.php';

/**
 * Description of DataContainer
 *
 * @author bbojanowicz
 */
abstract class DataContainer {
	
	/**
	 *
	 * @var type 
	 */
	protected $_values = array();
	
	/**
	 * @return array
	 */
	public abstract function getSupportedKeys();
	
	/**
	 * 
	 * @param string $name
	 * @param string $value
	 * @return \ifirma\DataContainer
	 * @throws IfirmaException
	 */
	public function __set($name, $value) {
		if(!in_array($name, $this->getSupportedKeys())){
			throw new IfirmaException(sprintf("Unsupported key: %s", $name)); 
		}
		
		$this->_values[$name] = $value;
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return string|null
	 * @throws IfirmaException
	 */
	public function __get($name) {
		if(!in_array($name, $this->getSupportedKeys())){
			throw new IfirmaException(sprintf("Unsupported key: %s", $name));
		}
		
		return (
				isset($this->_values[$name])
				? 
				$this->_values[$name]
				:
				null
			);
	}

	/**
	 * 
	 * @param \ifirma\DataContainer $container
	 * @param array $keysToFilter
	 * @return array
	 */
	public static function filterSupportedKeys(DataContainer $container, $keysToFilter){
		return array_filter($keysToFilter, function($value) use ($container){ return in_array($value, $container->getSupportedKeys());});
	}
}

