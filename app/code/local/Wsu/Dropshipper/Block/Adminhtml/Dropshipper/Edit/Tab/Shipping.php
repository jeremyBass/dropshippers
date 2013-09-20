<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
class Wsu_Dropshipper_Block_Adminhtml_Dropshipper_Edit_Tab_Shipping extends Mage_Adminhtml_Block_Widget_Form
{
   public function getAllShippingMethods(){
		$methods = Mage::getSingleton('shipping/config')->getActiveCarriers();
	
		$options = array();
	
		foreach($methods as $_ccode => $_carrier){
			$_methodOptions = array();
			if($_methods = $_carrier->getAllowedMethods()){
				foreach($_methods as $_mcode => $_method){
					$_code = $_ccode . '_' . $_mcode;
					$_methodOptions[] = array('value' => $_code, 'label' => $_method);
				}
	
				if(!$_title = Mage::getStoreConfig("carriers/$_ccode/title"))
					$_title = $_ccode;
	
				$options[] = array('value' => $_methodOptions, 'label' => $_title);
			}
		}
		return $options;
	} 
	

	
  protected function _prepareForm(){
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('dropshipper_form', array('legend'=>Mage::helper('wsu_dropshipper')->__('Dropshipper information')));
     
      $fieldset->addField('name', 'text', array(
          'label'     => Mage::helper('wsu_dropshipper')->__('Supplier Name'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'     => 'supplier_name'  
      ));
      
      $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('wsu_dropshipper')->__('Choose Shipping method'),
          'name'      => 'shipping_id',
          'values'    => $this->getAllShippingMethods()
      ));
     
     
      if ( Mage::getSingleton('adminhtml/session')->getDropshipperData() ){
          $form->setValues(Mage::getSingleton('adminhtml/session')->getDropshipperData());
          Mage::getSingleton('adminhtml/session')->setDropshipperData(null);
      } elseif ( Mage::registry('dropshipper_data') ) {
          $form->setValues(Mage::registry('dropshipper_data')->getData());
      }
      return parent::_prepareForm();
  }
}
