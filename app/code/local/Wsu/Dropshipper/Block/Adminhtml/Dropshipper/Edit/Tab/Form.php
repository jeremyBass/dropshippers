<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Block_Adminhtml_Dropshipper_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form {
    
    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $this->setForm($form);
        $fieldset = $form->addFieldset('dropshipper_form', array(
            'legend' => Mage::helper('wsu_dropshipper')->__('Dropshipper information')
        ));
        
        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Supplier Name'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'supplier_name'
        ));
        
        $fieldset->addField('label', 'label', array(
            'value' => Mage::helper('wsu_dropshipper')->__('Account information')
        ));
        $fieldset->addField('email', 'text', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Help Email'),
            'class' => 'required-entry',
            'required' => true,
            'name' => 'email'
        ));
        
        $fieldset->addField('contact_email', 'text', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Contact Email'),
            //'class' => 'required-entry',
            //'required' => true,
            'name' => 'contact_email'
        ));
        $fieldset->addField('contact_name', 'text', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Contact Name'),
            //'class' => 'required-entry',
            //'required' => true,
            'name' => 'contact_name'
        ));

        $fieldset->addField('account_id', 'text', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Account ID'),
            'class' => '',
            //'required' => true,
            'name' => 'account_id'
        ));
        /* at a later point
		$fieldset->addField('ds_icon', 'image', array(
			'label' => Mage::helper('wsu_dropshipper')->__('Dropshipper Icon'),
			'value'     => 'http://wsu.edu/logo.png',
        ));*/
        $fieldset->addField('contactInfo', 'label', array(
            'value' => Mage::helper('wsu_dropshipper')->__('Contact Address')
        ));
        
        $fieldset->addField('address', 'text', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Street'),
            'name' => 'address'
        ));
        
        $fieldset->addField('city', 'text', array(
            'label' => Mage::helper('wsu_dropshipper')->__('City'),
            'name' => 'city'
        ));
        
        $fieldset->addField('state', 'text', array(
            'label' => Mage::helper('wsu_dropshipper')->__('State'),
            'name' => 'state'
        ));
        
        $fieldset->addField('country', 'text', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Country'),
            'name' => 'country'
        ));
        
        $fieldset->addField('status', 'select', array(
            'label' => Mage::helper('wsu_dropshipper')->__('Status'),
            'name' => 'status',
            'values' => array(
                array(
                    'value' => 1,
                    'label' => Mage::helper('wsu_dropshipper')->__('Enabled')
                ),
                
                array(
                    'value' => 2,
                    'label' => Mage::helper('wsu_dropshipper')->__('Disabled')
                )
            )
        ));
        
        
        if (Mage::getSingleton('adminhtml/session')->getDropshipperData()) {
            $form->setValues(Mage::getSingleton('adminhtml/session')->getDropshipperData());
            Mage::getSingleton('adminhtml/session')->setDropshipperData(null);
        } elseif (Mage::registry('dropshipper_data')) {
            $form->setValues(Mage::registry('dropshipper_data')->getData());
        }
        return parent::_prepareForm();
    }
}
