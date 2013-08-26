<?php



class Mage_Core_Model_Resource_Ifirma extends Mage_Core_Model_Resource_Db_Abstract{
	/**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('ifirma/ifirma', 'id');
    }

}