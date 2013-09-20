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
class Wsu_Dropshipper_Block_Adminhtml_Widget_Grid_Column_Renderer_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input
{
    public function render(Varien_Object $row)
    {
        $productId = $row->getData('entity_id');
        $dropshippersId = $row->getData('dshipper_id');
        
        $val = '';$class = '';
        switch ($this->getColumn()->getId()) {
            case 'price':
                $class = $this->getColumn()->getInlineCss(). ' required-entry validate-zero-or-greater required-entry input-text';
                $val = number_format($row->getData($this->getColumn()->getIndex()), 2, '.', '');

                $html = '<input type="text" ';
                $html .= 'name="update_data[' . $this->getColumn()->getId() . ']['.$productId.']" ';
                $html .= 'value="' . $val . '"';
                $html .= 'maxlength="10"';
                $html .= 'class="' . $class . '"/>';
                
                break;
            case 'cost':
                $class = $this->getColumn()->getInlineCss(). ' required-entry validate-zero-or-greater required-entry input-text';
                $val = number_format($row->getData($this->getColumn()->getIndex()), 2, '.', '');

                $html = '<input type="text" ';
                $html .= 'name="update_data[' . $this->getColumn()->getId() . ']['.$productId.']" ';
                $html .= 'value="' . $val . '"';
                $html .= 'maxlength="10"';
                $html .= 'class="' . $class . '"/>';
                
                break;
            
            case 'qty':
                $class = $this->getColumn()->getInlineCss(). ' validate-number input-text';
                $decimals = strstr($row->getData($this->getColumn()->getIndex()), '.');
                $val = ($decimals > 0) ? number_format($row->getData($this->getColumn()->getIndex()),2,'.', '') : number_format($row->getData($this->getColumn()->getIndex()),0,'.', '');
                
                $html = '<input type="text" ';
                $html .= 'id="' . $this->getColumn()->getId() . '-' . $row->getData('id').'" ';
                $html .= 'name="update_data[' . $this->getColumn()->getId() . ']['.$productId.']" ';
                $html .= 'value="' . $val . '"';
                $html .= 'class="' . $class . '"/>';
                
                break;
        }
        
        return $html;
    }
}