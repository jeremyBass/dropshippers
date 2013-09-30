<?php
/**
 * Magento
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */

/**
 * Product in Dropshipper from
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Block_Adminhtml_Dropshipper_Allprolist_Tab_Product extends Mage_Adminhtml_Block_Widget_Grid {
    protected function _getStore() {
        $storeId = (int) $this->getRequest()->getParam('store', 0);
        return Mage::app()->getStore($storeId);
    }
    
    public function __construct() {
        parent::__construct();
        $this->setId('dropshipper_products');
        $this->setDefaultSort('entity_id');
        $this->setUseAjax(true);
		$this->setSaveParametersInSession(true);

		if ($this->getDropshipper()->getId()) {
			$this->setDefaultFilter(array(
				'in_products' => 1
			));
		}
		if ($this->isReadonly()) {
			$this->setFilterVisibility(false);
		}
    }
    
    public function getDropshipper() {
        return Mage::registry('dropshipper_data');
    }
    
    protected function _addColumnFilterToCollection($column) {
        // Set custom filter for in product flag
        if ($column->getId() == 'in_products') {
            $productIds = $this->_getSelectedProducts();
            if (empty($productIds)) {
                $productIds = 0;
            }
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter('entity_id', array(
                    'in' => $productIds
                ));
            } else {
                if ($productIds) {
                    $this->getCollection()->addFieldToFilter('entity_id', array(
                        'nin' => $productIds
                    ));
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }
    protected function _prepareCollection() {
        $store      = $this->_getStore();
        $collection = Mage::getModel('catalog/product')
						->getCollection()->addAttributeToSelect('name')
						->addAttributeToSelect('sku')
						->addAttributeToSelect('price')
						->addAttributeToSelect('cost')
						->addAttributeToFilter('type_id', Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
						->addStoreFilter($this->getRequest()->getParam('store'));
        
		
		/* this should be done if this is a new item.  Move this out */
        if (Mage::helper('catalog')->isModuleEnabled('Mage_CatalogInventory')) {
            $collection->joinField('qty', 'cataloginventory/stock_item', 'qty', 'product_id=entity_id', '{{table}}.stock_id=1', 'left');
        }
        /*we shouldn't care if it's visiable or not.
        $collection->joinAttribute('visibility', 'catalog_product/visibility', 'entity_id', null, 'inner', $store->getId());
        */
        if ($this->isReadonly()) {

        }
		$productIds = $this->_getSelectedProducts();
		if (empty($productIds)) {
			$productIds = array( 0 );
		}
		$collection->addFieldToFilter('entity_id', array( 'nin' => $productIds ));
		
		
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly() {
        return false;
    }
    protected function _prepareColumns() {
        $this->addColumn('entity_id', array(
            'header' => Mage::helper('wsu_dropshipper')->__('ID'),
            'sortable' => true,
            'width' => '60',
            'index' => 'entity_id'
        ));
        
        $this->addColumn('name', array(
            'header' => Mage::helper('wsu_dropshipper')->__('Name'),
            'index' => 'name'
        ));
        
        $this->addColumn('sku', array(
            'header' => Mage::helper('wsu_dropshipper')->__('SKU'),
            //'renderer' => 'Wsu_Dropshipper_Block_Adminhtml_Widget_Grid_Column_Renderer_Input',
            'sortable' => true,
            //'width' => '140',
            'index' => 'sku'
        ));

        
		$this->addColumn('action',
            array(
            'header'    =>  Mage::helper('wsu_dropshipper')->__('Action'),
			'header_css_class' => 'a-center',
			'align' => 'center',
            'width'     => '100',
            'type'      => 'action',
            'getter'    => 'getId',
            'actions'   => array(
				array(
					'caption'    => Mage::helper('wsu_dropshipper')->__('Add to Dropshipper'),
					'url'       => array(
						'base'=> '*/*/add_item',
						'params'=>array('id'=>Mage::registry('dropshipper_data')->getDshipper_id())
					),
					'field'     => 'entity_id'
				)
            ),
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'products',
            'is_system' => true,
    	));

        return parent::_prepareColumns();
    }
    
    public function getGridUrl() {
        return $this->getUrl('*/*/productGrid', array(
            '_current' => true
        ));
    }
    
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
 
 
	    
    protected function _prepareMassaction() {
        $this->setMassactionIdField('dshipper_id');
        $this->getMassactionBlock()->setFormFieldName('dropshipper');
        
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('wsu_dropshipper')->__('Are you sure?')
        ));
        
        $statuses = Mage::getSingleton('wsu_dropshipper/status')->getOptionArray();
        
        array_unshift($statuses, array(
            'label' => '',
            'value' => ''
        ));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array(
                '_current' => true
            )),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('wsu_dropshipper')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        
        $this->getMassactionBlock()->addItem('update_price', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Update Price'),
            'url' => $this->getUrl('*/*/massUpdatePrice', array(
                '_current' => true
            )),
            'additional' => array(
                'percent_price' => array(
                    'name' => 'percent',
                    'type' => 'text',
                    'class' => 'required-entry',
                    'label' => Mage::helper('wsu_dropshipper')->__('Percentage')
                )
            )
        ));
        
        return $this;
    }
    
    public function getRowUrl($row) {
        return $this->getUrl('*/*/add_item', array(
            'id' => $row->getId()
        ));
    }   
	
	
	
	/* functions to fill out */
	//maybe not here. but in the model
	//get the product with its
	//dropshipping values
	protected function getDropshippingProduct($product_id=0,$dropshipping_id=0){
	//return the product object with the new values 	
	}
	
	//let this be call by id
	protected function getProductAttrVal($product_id=0,$attr=null){
	
		//return the value	
	}
	
	
	//get by id or sku
	protected function getProductObject($product_id){
		
	}
}
