<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Model_Product extends Mage_Core_Model_Abstract {
    protected $_allowAttributes = array('price', 'cost', 'qty', 'sku');
    
    public function _construct() {
        parent::_construct();
        $this->_init('wsu_dropshipper/product');
    }
    
    public function saveProductRelations($dropshipper_Id, $newProductArray) {
        $this->getResource()->saveProductRelations($dropshipper_Id, $newProductArray);
    }
    
	public function reomove_item($product_id, $dropshipper_id){		
		return $this->getResource()->reomoving_item($product_id, $dropshipper_id);
	}
	public function add_item($product_id, $dropshipper_id){		
		return $this->getResource()->adding_item($product_id, $dropshipper_id);
	}	
	
    public function updateProducts($dropshipper_Id, $object) {
        
        $productData     = $object['update_data'];
        $ProductInstance = Mage::getModel('wsu_dropshipper/product');
        
        $newProductArray = array();
        foreach ($productData as $key => $value) {
            foreach ($value as $id => $_val) {
                $newProductArray[$id][$key] = $_val;
            }
        }
		var_dump($object);
        print("<hr/><br/><hr/>");
		var_dump($newProductArray);
		
		die();exit();
        $ProductInstance->saveProductRelations($dropshipper_Id, $newProductArray);
        
        //print_r($newProductArray);die(); 
        foreach ($newProductArray as $productId => $val) {
            /* no no no not today
            $product = Mage::getModel('catalog/product');
            $product->setStoreId(Mage_Core_Model_App::ADMIN_STORE_ID)
            ->load($productId);    
            
            $stockItem = Mage::getModel('cataloginventory/stock_item')->loadByProduct($productId);
            
            foreach($this->_allowAttributes as $_attribute)
            {
            if($val[$_attribute] != '')
            {
            if($_attribute == 'qty')
            {
            $stockItem->setData('qty', $val[$_attribute]);
            $stockItem->save();
            }else{
            $func = 'set'.ucfirst($_attribute); 
            $product->$func($val[$_attribute]);
            $product->save();
            }
            
            
            }
            }
            */
        }
    }
    
    public function getDropshipper($product) {
        $dropShipperData = Mage::getModel('wsu_dropshipper/product')->getCollection()->addFieldToFilter('product_id', $product->getId())->getFirstItem();
        return $dropShipperData;
    }
    
    public function updateProductPrice($dropshipperId, $percentage) {
        $this->getResource()->updateProductPrice($dropshipperId, $percentage);
    }
}
