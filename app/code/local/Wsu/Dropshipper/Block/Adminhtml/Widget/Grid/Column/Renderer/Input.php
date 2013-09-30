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
class Wsu_Dropshipper_Block_Adminhtml_Widget_Grid_Column_Renderer_Input extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Input {
    public function render(Varien_Object $row) {
        $productId      = $row->getData('entity_id');
        $dropshippersId = $row->getData('dshipper_id');
        
        $val   = '';
        $class = '';
		$col = $this->getColumn();
		$colId = $col->getId();
		$colIndex = $col->getIndex();
        switch ($colId) {
            
            case 'price':
                $class = $col->getInlineCss() . ' required-entry validate-zero-or-greater required-entry input-text';
                $val   = number_format($row->getData($colIndex), 2, '.', '');
                
                $html = '<input type="text" ';
                $html .= 'name="update_data[' . $colId . '][' . $productId . ']" ';
                $html .= 'value="' . $val . '" ';
                $html .= 'maxlength="10" ';
                $html .= 'class="' . $class . '"/>';
                
                break;
            case 'cost':
                $class = $col->getInlineCss() . ' required-entry validate-zero-or-greater required-entry input-text';
                $val   = number_format($row->getData($colIndex), 2, '.', '');
                
                $html = '<input type="text" ';
                $html .= 'name="update_data[' . $colId . '][' . $productId . ']" ';
                $html .= 'value="' . $val . '" ';
                $html .= 'maxlength="10" ';
                $html .= 'class="' . $class . '"/>';
                
                break;
            
            case 'qty':
                $class    = $col->getInlineCss() . ' validate-number input-text';
                $decimals = strstr($row->getData($colIndex), '.');
                $val      = ($decimals > 0) ? number_format($row->getData($colIndex), 2, '.', '') : number_format($row->getData($colIndex), 0, '.', '');
                
                $html = '<input type="text" ';
                $html .= 'id="' . $colId . '-' . $row->getData('id') . '" ';
                $html .= 'name="update_data[' . $this->getColumn()->getId() . '][' . $productId . ']" ';
                $html .= 'value="' . $val . '" ';
                $html .= 'class="' . $class . '"/>';
                
                break;
            case 'sku':
                $class = $col->getInlineCss() . ' input-text';
                $val   = $row->getData($colIndex);
                
                $html = '<input type="text" ';
                $html .= 'id="' . $colId . '-' . $row->getData('id') . '" ';
                $html .= 'name="update_data[' . $colId . '][' . $productId . ']" ';
                $html .= 'value="' . $val . '" ';
                $html .= 'style="width:140px" class="' . $class . '"/>';
                
                break;
        }
        
        return $html;
    }
}