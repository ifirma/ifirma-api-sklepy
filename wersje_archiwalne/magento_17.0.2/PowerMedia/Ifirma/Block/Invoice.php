<?php

/**
 * Description of Invoice
 *
 * @author bbojanowicz
 */
class PowerMedia_Ifirma_Block_Invoice extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface{
	
	/**
     * Retrieve order model instance
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return Mage::registry('current_order');
    }

	protected function _construct(){
		parent::_construct();
		$this->setTemplate('ifirma/invoice.phtml');
	}
	
    public function getSource()
    {
        return $this->getOrder();
    }
	
	public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
	
	public function getTabLabel()
    {
        return $this->__( 'Fakturowanie iFirma' );
    }

    public function getTabTitle()
    {
        return $this->__( 'Fakturowanie iFirma' );
    }
}

