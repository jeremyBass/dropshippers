<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
class Wsu_Dropshipper_Model_Resource_Shipping extends Mage_Core_Model_Resource_Db_Abstract{
    
    public function _construct(){    
        // Note that the dshipper_id refers to the key field in your database table.
        $this->_init('wsu_dropshipper/shipping', 'dshipper_shipping_id');
    }
}