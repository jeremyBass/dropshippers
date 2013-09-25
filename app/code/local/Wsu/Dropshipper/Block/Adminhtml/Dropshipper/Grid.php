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
        $this->setId('dropshipperGrid');
        $this->setDefaultSort('dshipper_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
    
    protected function _prepareCollection() {
        $collection = Mage::getModel('wsu_dropshipper/dropshipper')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }
    
    protected function _prepareColumns() {
        $this->addColumn('dshipper_id', array(
            'header' => Mage::helper('wsu_dropshipper')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'dshipper_id'
        ));
        
        $this->addColumn('name', array(
            'header' => Mage::helper('wsu_dropshipper')->__('Dropshipper'),
            'align' => 'left',
            'index' => 'name'
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('wsu_dropshipper')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled'
            )
        ));
        
        $this->addColumn('action', array(
            'header' => Mage::helper('wsu_dropshipper')->__('Action'),
            'width' => '100',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('wsu_dropshipper')->__('Edit'),
                    'url' => array(
                        'base' => '*/*/edit'
                    ),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true
        ));
        
        $this->addExportType('*/*/exportCsv', Mage::helper('wsu_dropshipper')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('wsu_dropshipper')->__('XML'));
        
        return parent::_prepareColumns();
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
        return $this->getUrl('*/*/edit', array(
            'id' => $row->getId()
        ));
    }
    
}