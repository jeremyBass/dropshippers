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
	
	//remove the item altogether
	public function reomoving_item($product_id=0, $dropshipper_id=0){	
   		if($product_id==0||$dropshipper_id==0) return false;
		$writeAdapter = $this->_getWriteAdapter();
		$writeAdapter->delete($this->getTable('wsu_dropshipper/product'), array(
			'dshipper_id = ?' => $dropshipper_id,
			'product_id = ?' => $product_id
		));
		return Mage::getModel('wsu_dropshipper/product')->load($product_id)->getId()>0?false:true;
	}
	
	//quick add the product to the dropshipper
	//update values later
	public function adding_item($product_id=null, $dropshipper_id=0){	
   		if($product_id==null||$dropshipper_id==0) return false;
		$productTable = $this->getTable('wsu_dropshipper/product');
		$writeAdapter = $this->_getWriteAdapter();
			$data                = array();
			$data['product_id']  = $product_id;
			$data['dshipper_id'] = $dropshipper_id;
		$writeAdapter->insert($productTable, $data);
		return Mage::getModel('wsu_dropshipper/product')->load($product_id)->getId()>0?false:true;
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
}