<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */

class Wsu_Dropshipper_Block_Adminhtml_Sales_Order_View_Tab_Dropshipper extends Mage_Adminhtml_Block_Sales_Order_Abstract implements Mage_Adminhtml_Block_Widget_Tab_Interface {
    public function __construct() {
        parent::__construct();
        $this->setTemplate('dropshipper/order/view/products.phtml');
    }
    
    public function getOrder() {
        return Mage::registry('current_order');
    }
    
    public function getConnection() {
        return Mage::getSingleton('core/resource')->getConnection('catalog_write');
    }
    
    public function getDropshippersOrderedItems() {
        $order = $this->getOrder();
        $order = Mage::getModel('sales/order')->load($order->getId());
        $items = $order->getItemsCollection(array(
            Mage_Catalog_Model_Product_Type::TYPE_SIMPLE
        ));
        return $items;
    }
    
    public function getSource() {
        return $this->getOrder();
    }
    
    
    public function getTabLabel() {
        return Mage::helper('sales')->__('Dropshippers');
    }
    
    public function getTabTitle() {
        return Mage::helper('sales')->__('Dropshippers');
    }
    
    public function canShowTab() {
        return true;
    }
    
    public function isHidden() {
        return false;
    }
    
    public function getLocation() {
        return $this->getUrl('dropshipper/adminhtml_order/saveorder', array(
            'order_id' => $this->getRequest()->getParam('order_id')
        ));
    }
    
    public function sendMailAction() {
        return $this->getUrl('dropshipper/adminhtml_order/sendmail', array(
            'order_id' => $this->getRequest()->getParam('order_id')
        ));
    }
    
    
}
