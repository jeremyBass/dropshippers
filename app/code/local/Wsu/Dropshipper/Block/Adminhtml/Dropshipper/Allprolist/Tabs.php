<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Block_Adminhtml_Dropshipper_Allprolist_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    
    public function __construct() {
        parent::__construct();
        $this->setId('dropshipper_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('wsu_dropshipper')->__('Dropshipper Information'));
    }
    
    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->addItem('skin_js', 'dropshipper/product.js');
        parent::_prepareLayout();
    }
    protected function _beforeToHtml() {
        $blocks = $this->getOutputBlock();
        $this->addTab('products', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Products'),
            'content' => $this->_outputBlocks($blocks['usedgridBlock'],$blocks['serializerBlock'])
        ));
        return parent::_beforeToHtml();
    }
    
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray) {
        return $this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_allprolist_tab_ajax_serializer')->setGridBlock($gridBlock)->setProducts($productsArray)->setInputElementName($inputName)->setAttributes(array(
            'status'
        ));
    }
    
    /**
     * Output specified blocks as a text list
     */
    protected function _outputBlocks() {
        $blocks = func_get_args();
        $output = $this->getLayout()->createBlock('adminhtml/text_list');
        foreach ($blocks as $block) {
            $output->insert($block, '', true);
        }
        return $output->toHtml();
    }
    
    protected function getOutputBlock() {
		$used_product = null;
		$unused_product = null;
		
		$dshipper_id = Mage::registry('dropshipper_data')->getDshipper_id();

        $collection = Mage::getModel('catalog/product')
						->getCollection()->addAttributeToSelect('name')
						->addAttributeToSelect('sku')
						->addAttributeToSelect('price')
						->addAttributeToSelect('cost')
						->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE);
		$store_id = $this->getRequest()->getParam('store');				
		if($store_id)$collection->addStoreFilter( $store_id );

		if ($dshipper_id) {
			$collection->getSelect()->join(
				array( 'dshipproduct' => $collection->getTable('wsu_dropshipper/product') ),
				sprintf('e.entity_id = dshipproduct.product_id AND dshipproduct.dshipper_id = %d', $dshipper_id)
			);
        }
		
        /*we shouldn't care if it's visiable or not.
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
        */

		$productIds = $this->_getSelectedProducts();
		if (empty($productIds)) {
			$productIds = array( 0 );
		}
		$collection->addFieldToFilter('entity_id', array( 'in' => $productIds ));

        $usedgridBlock       = $this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_allprolist_tab_product')
			->setGridUrl($this->getUrl('*/*/productGrid', array(
            '_current' => true
        )));
		//var_dump($usedgridBlock);die();
        // holds the selected rows ids    
        $serializerBlock = $this->_createSerializerBlock('products', $usedgridBlock, $collection);
        return array(
            'usedgridBlock' => $usedgridBlock,
            'serializerBlock' => $serializerBlock
        );
    }
    
	
	//*these function here need to be replaced.  They are already used a few times so abstract it and get then centralized
	protected function _getSelectedProducts() {
        $products = $this->getProductsSelected();
        return $products;
    }
    protected function getProductsSelected() {
        $dshipper_id = Mage::registry('dropshipper_data')->getDshipper_id();
        if ($dshipper_id>0) {
            $products    = Mage::getModel('catalog/product')->getCollection();
            
            //$products->addAttributeToFilter('visibility',array('neq'=>1));
            $products->getSelect()->join(array(
                'dshipproduct' => $products->getTable('wsu_dropshipper/product')
            ), 'e.entity_id = dshipproduct.product_id AND dshipproduct.dshipper_id = ' . $dshipper_id);
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
            $products = $products->getAllIds();
        } else {
            $products = null;
        }
        return $products;
    }
}