<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
class Wsu_Dropshipper_Block_Adminhtml_Dropshipper extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_dropshipper';
    $this->_blockGroup = 'wsu_dropshipper';
    $this->_headerText = Mage::helper('wsu_dropshipper')->__('Dropshipper Manager');
    $this->_addButtonLabel = Mage::helper('wsu_dropshipper')->__('Add');
    parent::__construct();
  }
}