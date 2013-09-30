<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Block_Adminhtml_Dropshipper_Grid extends Mage_Adminhtml_Block_Widget_Grid {
    public function __construct() {
        parent::__construct();
        $this->setId('productGrid');
        $this->setDefaultSort('sku');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection() {
        $products    = Mage::getModel('catalog/product')->getCollection();
		$dshipper_id = Mage::registry('dropshipper_data')->getDshipper_id();
 		if ($dshipper_id) {
			$products->getSelect()->join(array(
                'dshipproduct' => $products->getTable('wsu_dropshipper/product')
            ), 'e.entity_id = dshipproduct.product_id AND dshipproduct.dshipper_id = ' . $dshipper_id);
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
			$products = $products->getAllIds();
        }
        $this->setCollection($products);
        return parent::_prepareCollection();
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
            'renderer' => 'Wsu_Dropshipper_Block_Adminhtml_Widget_Grid_Column_Renderer_Input',
            'sortable' => true,
            'width' => '140',
            'index' => 'sku'
        ));

        
        $this->addColumn('action', array(
            'header' => Mage::helper('wsu_dropshipper')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('wsu_dropshipper')->__('Add'),
                    'url' => array(
                        'base' => '*/*/add_item'
                    ),
                    'field' => 'product_id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true
        ));

        return parent::_prepareColumns();
    }
	
	
    protected function _getSelectedProducts() {
        $products = $this->getProductsSelected();
        return $products;
    }
    protected function getProductsSelected() {
        
        if (Mage::registry('dropshipper_data')->getDshipper_id()) {
            $products    = Mage::getModel('catalog/product')->getCollection();
            $dshipper_id = Mage::registry('dropshipper_data')->getDshipper_id();
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
    
}