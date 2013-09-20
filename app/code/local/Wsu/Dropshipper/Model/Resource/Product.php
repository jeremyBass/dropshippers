<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
class Wsu_Dropshipper_Model_Resource_Product extends Mage_Core_Model_Resource_Db_Abstract
{
    
    public function _construct()
    {    
        // Note that the dshipper_id refers to the key field in your database table.
        $this->_init('wsu_dropshipper/product', 'dshipper_product_id');
    }
    
    public function saveProductRelations($dropshipper)
    {               
        $productTable = $this->getTable('wsu_dropshipper/product');
        $readAdapter = $this->_getReadAdapter();
        $writeAdapter = $this->_getWriteAdapter();
        $products = array_keys($dropshipper->getProducts());     
        $dropshipper_Id     = $dropshipper->getDshipperId();
        
        $select = $readAdapter->select()
                        ->from(
                            array('product_table'=>$productTable),
                            array('dshipper_product_id')
                        )
                        ->where('product_table.dshipper_id = ?',$dropshipper_Id);
        if(count($products))
            $select->where('product_table.product_id NOT IN(?)',$products);
        
        $oldProduct =  $readAdapter->fetchCol($select);
        if(count($oldProduct)){
            $writeAdapter->delete(
                $this->getTable('wsu_dropshipper/product'),
                array(
                    'dshipper_product_id IN (?)' => $oldProduct,
                )
            );
        }
            
        foreach($products as $key=>$productId){
            $data = array();
            $data['product_id']     = $productId;
            $data['dshipper_id'] = $dropshipper_Id;
            $writeAdapter->insertOnDuplicate($productTable,$data);
        }
                              
        return true;
    }
    
    public function updateProductPrice($dropshipperId,$percentage)
    {
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