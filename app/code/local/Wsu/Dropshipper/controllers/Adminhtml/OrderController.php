<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
class Wsu_Dropshipper_Adminhtml_OrderController extends Mage_Adminhtml_Controller_action
{
    const XML_PATH_EMAIL_DROPSHIPPER_TEMPLATE       = 'wsu_dropshipper_email/dropshipper/wsu_dropshipper_template';
    const XML_PATH_EMAIL_DROPSHIPPER_SENDER         = 'trans_email/ident_dropshipper/email';
    const XML_PATH_EMAIL_DROPSHIPPER_SENDER_NAME    = 'trans_email/ident_dropshipper/name';
    const XML_PATH_DROPSHIPPER_EMAIL_OPTION         = 'dropshipper/general/send_mail';
        
    public function saveorderAction(){
       if( $data = $this->getRequest()->getParams()){
           try{
                foreach($data['dropshipper'] as $key=>$_dropshipper){                       
                    if($_dropshipper['id'] && $_dropshipper['item_id']){
                        $dropshipper = Mage::getModel('wsu_dropshipper/order')->getCollection()
                                                ->getItemByColumnValue('item_id',$_dropshipper['item_id']);   
                        if(!$dropshipper){
                            $newData =  array();
                            $newData['dshipper_id']  = $_dropshipper['id'];
                            $newData['item_id']         = $_dropshipper['item_id'];
                            $model = Mage::getModel('wsu_dropshipper/order');    
                            $model->setData($newData);
                            $model->save();
                        }
                        else{
                           $dropshipper ->setDshipperId($_dropshipper['id'])
                                        ->save();
                        }          
                    }
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('wsu_dropshipper')->__('Dropshipper successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);
           }
           catch (Exception $e) {
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    Mage::getSingleton('adminhtml/session')->setFormData($data);                                                                                
                    return $this->_redirect('adminhtml/sales_order/view', array('order_id'=> $this->getRequest()->getParam('order_id'),'_current'=>true,'tab'=>'sales_order_view_tabs_dropshipper'));
               }               
            
       }
       return $this->_redirect('adminhtml/sales_order/view', array('order_id'=> $this->getRequest()->getParam('order_id'),'_current'=>true,'tab'=>'sales_order_view_tabs_dropshipper'));
       
    }
    
    public function sendmailAction() 
    {
        if ($data = $this->getRequest()->getPost()) 
        {
            
            //Assignment of Dropshipper
            
            foreach($data['dropshipper'] as $key=>$_dropshipper){                       
                if($_dropshipper['id'] && $_dropshipper['item_id']){
                    $dropshipper = Mage::getModel('wsu_dropshipper/order')->getCollection()
                                            ->getItemByColumnValue('item_id',$_dropshipper['item_id']);   
                    if(!$dropshipper){
                        $newData =  array();
                        $newData['dshipper_id']  = $_dropshipper['id'];
                        $newData['item_id']         = $_dropshipper['item_id'];
                        $model = Mage::getModel('wsu_dropshipper/order');    
                        $model->setData($newData);
                        $model->save();
                    }
                    else{
                       $dropshipper ->setDshipperId($_dropshipper['id'])
                                    ->save();
                    }          
                }
            }
            
            //End
            
            $dropShipperOrderResource = Mage::getResourceModel('wsu_dropshipper/order');
            $order = Mage::getModel('sales/order')->load($this->getRequest()->getParam('order_id'));
            $shipping_address = $order->getShippingAddress();
            
            $storeId = Mage::app()->getStore()->getId();            
            $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_DROPSHIPPER_TEMPLATE, $storeId);
            
            
            $itemIds = array();
            foreach($data['dropshipper'] as $key=>$_dropshipper){
                if($_dropshipper['id']){
                    $itemIds[] = $_dropshipper['item_id'];
                }
            }
            
            try{
                
                $dropshipperMailOption = Mage::getStoreConfig(self::XML_PATH_DROPSHIPPER_EMAIL_OPTION);
                if($dropshipperMailOption)
                {
                    //send mail to other dropshippers
                    
                    $_allDropShippers = $dropShipperOrderResource->getItemsOthersDropShipper($itemIds);                
                    if(!empty($_allDropShippers)){
                        foreach($_allDropShippers as $_dropshipper){
                            $items = explode(', ',$_dropshipper['items']);
                            $_itemCollection = Mage::getResourceModel('sales/order_item_collection')
                                                ->addFieldToFilter('item_id',array('in'=>$items))
                                                ->setOrderFilter($order);
                                                
                            $mailTemplate = Mage::getModel('core/email_template');
                            $mailTemplate   ->setDesignConfig(array('area' => 'frontend', 'store' => $storeId))
                                            ->sendTransactional(
                                                $templateId,
                                                array('email'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_DROPSHIPPER_SENDER),'name'=>Mage::getStoreConfig(self::XML_PATH_EMAIL_DROPSHIPPER_SENDER_NAME)),
                                                $_dropshipper['email'],
                                                $_dropshipper['name'],
                                                array(
                                                    'order'   => $order,
                                                    'items' => $_itemCollection,
                                                    'dropshipper_name' => $_dropshipper['name'],
                                                    'shipping_address' => $shipping_address,
                                                )
                                            );
                            if ($mailTemplate->getSentSuccess()) {
                                $dropShipperOrderResource->updateMailStatus($items);
                            }
                        }
                    }
                    
                     Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('wsu_dropshipper')->__('Dropshipper Mail successfully sent'));
                     Mage::getSingleton('adminhtml/session')->setFormData(false);                   
                    return $this->_redirect('adminhtml/sales_order/view', array('order_id'=> $this->getRequest()->getParam('order_id'),'_current'=>true,'tab'=>'sales_order_view_tabs_dropshipper'));
                    
                }else{
                    Mage::throwException('Email Communication Disabled For Dropshipper');
                }   
            }catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);                                                                
                $this->_redirect('adminhtml/sales_order/view', array('order_id'=> $this->getRequest()->getParam('order_id'),'_current'=>true,'tab'=>'sales_order_view_tabs_dropshipper'));
                return;
           }
        }
    }
    
}
    
