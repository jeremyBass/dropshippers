<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
class Wsu_Dropshipper_Model_Observer extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    const XML_PATH_EMAIL_DROPSHIPPER_TEMPLATE       = 'wsu_dropshipper_email/dropshipper/wsu_dropshipper_template';
    const XML_PATH_EMAIL_DROPSHIPPER_SENDER         = 'trans_email/ident_dropshipper/email';
    const XML_PATH_EMAIL_DROPSHIPPER_SENDER_NAME    = 'trans_email/ident_dropshipper/name';
    const DROPSHIPPER_EMAIL_OPTION         = 'dropshipper/general/send_mail';
    const DROPSHIPPER_ORDER_STATUS         = 'dropshipper/general/order_status';

	public function modifyPrice(Varien_Event_Observer $obs) {
		// Get the quote item
		$item = $obs->getQuoteItem();
		// Ensure we have the parent item, if it has one
		$item = ( $item->getParentItem() ? $item->getParentItem() : $item );
		// Load the custom price
		$price = $this->_getPriceByItem($item);
		if($price>0){
			// Set the custom price
			$item->setCustomPrice($price);
			$item->setOriginalCustomPrice($price);
			// Enable super mode on the product.
			$item->getProduct()->setIsSuperMode(true);
		}
	}
	protected function _getPriceByItem(Mage_Sales_Model_Quote_Item $item) {
		$id = $item->getProductId();      
		$_product  = Mage::getModel('catalog/product')->load($id);        
		$dropshipper = Mage::getModel('wsu_dropshipper/product')->getDropshipper($_product); // should be a list right?
		//then loop of the dropshippers 
		//while logging the lowest cost/price or what other logic 
		$price=0;
		if($_product ->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE && $dropshipper->getId()){
			 $price = $item->getPrice();
		}           
		//use $item to determine your custom price.
		
		return $price;
	}
	    
    public function addDropshipperItem(Varien_Event_Observer $observer)
    {
        if(Mage::helper("wsu_dropshipper")->isActiveDropshipper()){
            $order = $observer->getEvent()->getOrder();
            if($order)
            {
                $items = $order->getAllVisibleItems();
                foreach($items as $val)
                {
                    $id = $val->getProductId();      
                    $_product  = Mage::getModel('catalog/product')->load($id);
                    
                    $dropshipper = Mage::getModel('wsu_dropshipper/product')->getDropshipper($_product);
                    if($_product ->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE && $dropshipper->getId())
                    {
                         $data['dshipper_id'] = $dropshipper->getDshipperId();                         
                         $data['item_id'] = $val->getId();
                         $model = Mage::getModel('wsu_dropshipper/order');    
                         $model->setData($data);
                         $model->save();
                         
                    }         
                }
            }
            else
            {
                $orders = $observer->getEvent()->getOrders();
                foreach($orders as $_order)
                {
                    $items = $_order->getAllItems();
                    foreach($items as $val)
                    {
                        $id = $val->getProductId();      
                        $_product  = Mage::getModel('catalog/product')->load($id);
                    $dropshipper = Mage::getModel('wsu_dropshipper/product')->getDropshipper($_product);
                        if($_product ->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE && $dropshipper)
                        {
                             $data['dshipper_id'] = $dropshipper->getDshipperId();
                             $data['item_id'] = $val->getId();
                             $model = Mage::getModel('wsu_dropshipper/order');    
                             $model->setData($data);
                             $model->save();
                        }         
                    }
                }
            }        
        }
        return $this;
    }
    
    public function sendMail(Varien_Event_Observer $observer)
    {
        if(Mage::helper("wsu_dropshipper")->isActiveDropshipper()){
            
            $order = $observer->getEvent()->getOrder(); 
            $orderStatus = $order->getStatus();
            
            $dropshipperOrderStatus = Mage::getStoreConfig(self::DROPSHIPPER_ORDER_STATUS);
            $dropshipperMailOption = Mage::getStoreConfig(self::DROPSHIPPER_EMAIL_OPTION);
            
            if($orderStatus == $dropshipperOrderStatus && $dropshipperMailOption)
            {
                $dropShipperOrderResource = Mage::getResourceModel('wsu_dropshipper/order');
                $shipping_address = $order->getShippingAddress();
                $storeId = Mage::app()->getStore()->getId();            
                $templateId = Mage::getStoreConfig(self::XML_PATH_EMAIL_DROPSHIPPER_TEMPLATE, $storeId);
                $orderItems = $order->getAllItems();
                $itemIds = array();
                foreach($orderItems as $_item){
                    if($_item->getProductType() == Mage_Catalog_Model_Product_Type::TYPE_SIMPLE)
                        $itemIds[] = $_item->getId();
                }
                
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
            }
        }
    }
}