<?php
/**
 * Magento
 *
 * @category    Wsu    
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Model_Dropshipper extends Mage_Core_Model_Abstract {
    protected $_attributeOptionCollection = null;
    protected $_attribute = null;
    public function _construct() {
        parent::_construct();
        $this->_init('wsu_dropshipper/dropshipper');
    }
    
    protected function _afterSave() {
        
        //if($this->getProducts())
        //don't really need to check this do we?
        //$this->getProductInstance()->saveProductRelations($this);
        
        return parent::_afterSave();
    }
    
    protected function getProductInstance() {
        return Mage::getModel('wsu_dropshipper/product');
    }
}
