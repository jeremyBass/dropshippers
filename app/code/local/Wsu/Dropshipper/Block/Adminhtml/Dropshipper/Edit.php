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
					if($('#dialog-form').length<=0) $('body').append('<div id=\"allProductGrid\">hello</div><svg version=\"1.1\" xmlns=\"http://www.w3.org/2000/svg\"><filter id=\"blur\"><feGaussianBlur stdDeviation=\"3\"/></filter></svg>');
					var URL = '".$this->helper('adminhtml')->getUrl('dropshipper/adminhtml_dropshipper/allprolist', array('id' => Mage::registry('dropshipper_data')->getId()) )."';
					
					
					$('#allProductGrid').load(
					URL+' #dropshipper_products,#productGrid,.xdebug-error',
					function(){
						$('#allProductGrid').dialog({
							autoOpen: true,
							height: 300,
							width: 600,
							modal: true,
							drag:false,
							open:function(){
								$('.wrapper').attr('style','filter: blur(3px); -webkit-filter: blur(3px); -moz-filter: blur(3px); -o-filter: blur(3px); -ms-filter: blur(3px);filter: url(blur.svg#blur);filter:progid:DXImageTransform.Microsoft.Blur(PixelRadius=\"3\");')
								
							},
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
