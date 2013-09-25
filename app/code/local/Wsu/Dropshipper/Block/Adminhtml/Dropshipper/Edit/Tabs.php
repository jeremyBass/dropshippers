<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Block_Adminhtml_Dropshipper_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs {
    
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
        $this->addTab('form_section', array(
            'label' => Mage::helper('wsu_dropshipper')->__('General'),
            'title' => Mage::helper('wsu_dropshipper')->__('General'),
            'content' => $this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_edit_tab_form')->toHtml()
        ));
        
        $blocks = $this->getOutputBlock();
        $this->addTab('products', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Products'),
            'content' => $this->_outputBlocks($blocks['gridBlock'], $blocks['serializerBlock'])
        ));
        
        $this->addTab('shipping', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Shipping Method'),
            'title' => Mage::helper('wsu_dropshipper')->__('Shipping Method'),
            'content' => $this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_edit_tab_shipping')->toHtml()
        ));
        
        
        
        return parent::_beforeToHtml();
    }
    
    protected function _createSerializerBlock($inputName, Mage_Adminhtml_Block_Widget_Grid $gridBlock, $productsArray) {
        return $this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_edit_tab_ajax_serializer')->setGridBlock($gridBlock)->setProducts($productsArray)->setInputElementName($inputName)->setAttributes(array(
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
        if (Mage::registry('dropshipper_data')->getDshipper_id()) {
            $dshipper_id         = Mage::registry('dropshipper_data')->getDshipper_id();
            $dropshipper_product = Mage::getModel('catalog/product')->getCollection();
            //$dropshipper_product ->addAttributeToFilter('visibility',array('neq'=>1));
            
            /*  
            $dropshipper_product->getSelect()
            ->join(
            array('dshipproduct'=> $dropshipper_product->getTable('wsu_dropshipper/product')),
            'e.entity_id = dshipproduct.product_id AND dshipproduct.dshipper_id = '.$dshipper_id
            );*/
            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($dropshipper_product);
        } else {
            $dropshipper_product = null;
        }
        
        $gridBlock       = $this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_edit_tab_product')->setGridUrl($this->getUrl('*/*/productGrid', array(
            '_current' => true
        )));
        // holds the selected rows ids    
        $serializerBlock = $this->_createSerializerBlock('products', $gridBlock, $dropshipper_product);
        return array(
            'gridBlock' => $gridBlock,
            'serializerBlock' => $serializerBlock
        );
    }
    
}