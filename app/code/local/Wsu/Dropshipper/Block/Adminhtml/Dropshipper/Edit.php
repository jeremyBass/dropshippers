<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
 */
class Wsu_Dropshipper_Block_Adminhtml_Dropshipper_Edit extends Mage_Adminhtml_Block_Widget_Form_Container {
    public function __construct() {
        parent::__construct();
        
        $this->_objectId   = 'id';
        $this->_blockGroup = 'wsu_dropshipper';
        $this->_controller = 'adminhtml_dropshipper';
        
        $this->_addButton('saveandcontinue', array(
            'label' => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick' => 'saveAndContinueEdit()',
            'class' => 'save'
        ), -100);


        $this->_addButton('add_item', array(
            'label' => Mage::helper('adminhtml')->__('Add Item to List'),
            'onclick' => 'add_item()',
            'class' => 'save'
        ), -100);
        
        $this->_formScripts[] = "
        	function saveAndContinueEdit(){
                editForm.submit($('edit_form').action+'back/edit/');
            }
			function add_item(){
				(function($){
					if($('#dialog-form').length<=0) $('body').append('<div id=\"allProductGrid\">hello</div>');
					$('#allProductGrid').load(
					'".$this->helper('adminhtml')->getUrl('adminhtml/wsu_dropshipper/list')." #productGrid',
					function(){
						$('#allProductGrid').dialog({
							autoOpen: true,
							height: 300,
							width: 350,
							modal: true,
							drag:false,
							buttons: {
								'Create an account': function() {
									$( this ).dialog( 'close' );
								},
								Cancel: function() {
									$( this ).dialog( 'close' );
	
								}
							},
							close: function() {
								$( this ).dialog( 'destroy' );
							}
						});
					});
				})(jQuery);
			}
			(function($){
				$('button[title=\"Add Item to List\"]').hide();	
				$('.tab-item-link').on('click',function(){
					if($(this).is($('#dropshipper_tabs_products'))){
						$('button[title=\"Add Item to List\"]').show();
					}else{
						$('button[title=\"Add Item to List\"]').hide();	
					}
				});				
			})(jQuery);
        ";
    }
    
    public function getHeaderText() {
        if (Mage::registry('dropshipper_data') && Mage::registry('dropshipper_data')->getId()) {
            return Mage::helper('wsu_dropshipper')->__("Edit Dropshipper '%s'", $this->htmlEscape(Mage::registry('dropshipper_data')->getName()));
        } else {
            return Mage::helper('wsu_dropshipper')->__('Add Dropshipper');
        }
    }
}
