<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Model_Resource_Product extends Mage_Core_Model_Resource_Db_Abstract {
    
    public function _construct() {
        // Note that the dshipper_id refers to the key field in your database table.
        $this->_init('wsu_dropshipper/product', 'dshipper_product_id');
    }
    
    public function saveProductRelations($dropshipper_id, $products) {
        
        $productTable = $this->getTable('wsu_dropshipper/product');
        $readAdapter  = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();
        //clear all te old entries.. not the best but come back to 
        //@todo
        $writeAdapter->delete($this->getTable('wsu_dropshipper/product'), array(
            'dshipper_id = ?' => $dropshipper_id
        ));
        
        foreach ($products as $key => $product) {
            $data                = array();
            $data['product_id']  = $key;
            $data['dshipper_id'] = $dropshipper_id;
            $data['cost']        = $products[$key]['cost'];
            $data['price']       = $products[$key]['price'];
            $data['qty']         = $products[$key]['qty'];
            $data['sku']         = $products[$key]['sku'];
            $writeAdapter->insert($productTable, $data);
        }
        return true;
    }
    
    public function updateProductPrice($dropshipperId, $percentage) {
        /* Not realy that helpful yo
        $productTable = $this->getTable('wsu_dropshipper/product');
        $readAdapter = $this->_getReadAdapter();
        $select = $readAdapter->select()
        ->from(
        array('product_table'=>$productTable),
        array('dshipper_product_id')
        )
        ->where('product_table.dshipper_id = ?',$dropshipperId);
        
        $products = $readAdapter->fetchCol($select);                        
        foreach($products as $productId)
        {
        $product = Mage::getModel('catalog/product')->load($productId);
        if($product->getCost())
        {
        $changePrice = ($product->getCost())+(($product->getCost())*$percentage/100);
        $product->setPrice($changePrice); 
        $product->save();
        }
        
        }
        */
    }
}