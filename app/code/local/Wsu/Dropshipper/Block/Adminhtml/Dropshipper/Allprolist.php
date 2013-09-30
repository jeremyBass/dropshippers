<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Block_Adminhtml_Dropshipper_Allprolist extends Mage_Adminhtml_Block_Widget_Form_Container {
    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->_blockGroup = 'wsu_dropshipper';
        $this->_controller = 'adminhtml_dropshipper';
        //$this->setDefaultSort('sku');
        //$this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
 
    
}