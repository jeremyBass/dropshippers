<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Model_Resource_Order extends Mage_Core_Model_Resource_Db_Abstract {
    
    public function _construct() {
        // Note that the dshipper_id refers to the key field in your database table.
        $this->_init('wsu_dropshipper/order', 'dshipper_item_id');
    }
    
    public function getItemsOthersDropShipper($items = array()) {
        $dropShipperTable      = $this->getTable('dropshipper');
        $dropShipperOrderTable = $this->getTable('order');
        
        if (!empty($items)) {
            $select = $this->_getReadAdapter()->select();
            $select->from(array(
                'order' => $dropShipperOrderTable
            ), array(
                ''
            ))->columns(array(
                'items' => "GROUP_CONCAT(order.item_id SEPARATOR ', ')"
            ))->join(array(
                'dropshipper' => $dropShipperTable
            ), 'order.dshipper_id = dropshipper.dshipper_id', array(
                'name',
                'email'
            ))->where('order.item_id in(?)', $items)->group('dropshipper.dshipper_id');
            $data = $this->_getReadAdapter()->fetchAll($select);
            return $data;
        }
        return null;
    }
    
    public function updateMailStatus($items = array()) {
        $dropShipperOrderTable = $this->getTable('order');
        
        if (!empty($items)) {
            $whereCond = array(
                'item_id in(?)' => $items
            );
            $this->_getWriteAdapter()->update($dropShipperOrderTable, array(
                'mail_status' => 1
            ), $whereCond);
        }
        return $this;
        
    }
}