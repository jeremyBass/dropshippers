<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
class Wsu_Dropshipper_Model_Order extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('wsu_dropshipper/order');
    }
    
    public function getDropshipperByItem($itemId)
    {
        $dropshipper = Mage::getModel('wsu_dropshipper/order')->getCollection();
        $dropshipper = $dropshipper->getItemByColumnValue('item_id',$itemId);
        return $dropshipper;
    }    
}
