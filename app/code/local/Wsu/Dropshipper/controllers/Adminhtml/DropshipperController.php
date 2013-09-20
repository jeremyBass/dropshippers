<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
class Wsu_Dropshipper_Adminhtml_DropshipperController extends Mage_Adminhtml_Controller_action
{
    protected function _initAction() {
        
        $this->loadLayout()
            ->_setActiveMenu('catalog/dropshipper')
            ->_addBreadcrumb(Mage::helper('adminhtml')->__('Dropshipper Manager'), Mage::helper('adminhtml')->__('Dropshipper Manager'));
        
        return $this;
    }
    protected function _initDropshipper()
    {
        $id     = $this->getRequest()->getParam('id');
        $model  = Mage::getModel('wsu_dropshipper/dropshipper')->load($id);   
        $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
        if (!empty($data)) {
                $model->setData($data);
        }
        Mage::register('dropshipper_data', $model);
    }
    
    public function indexAction() {
        $this->_initAction()
            ->renderLayout();
    }
    public function editAction() {
        
        $this->_initDropshipper();
        
        $model  = Mage::registry('dropshipper_data');
        if ($model->getId() || (!isset($id) || $id == 0 )) {
            $this->loadLayout();
            $this->_setActiveMenu('catalog/dropshipper');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Dropshipper Item Manager'), Mage::helper('adminhtml')->__('Dropshipper Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Dropshipper Item News'), Mage::helper('adminhtml')->__('Dropshipper Item News'));
            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);
            $this->_addContent($this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_edit'))
                ->_addLeft($this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('wsu_dropshipper')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
    
    public function newAction() {
        $this->_forward('edit');
    }
    
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
           
            $data['name'] = $data['supplier_name'];
            $errors = $this->validate($data);
            if(!empty($errors)){
                foreach($errors as $error){
                    Mage::getSingleton('adminhtml/session')->addError($error);
                }
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
            $dropshipper_products = $this->_decodeInput($data['products']);
            $data['products'] = $dropshipper_products;
            
            if($data['update_data'])
                Mage::getModel('wsu_dropshipper/product')->updateProducts($data);
            
            $model = Mage::getModel('wsu_dropshipper/dropshipper');    
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));
            try {
                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('wsu_dropshipper')->__('Dropshipper was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('wsu_dropshipper')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
    
    public function deleteAction() {
        if( $this->getRequest()->getParam('id') > 0 ) {
            try {
                $model = Mage::getModel('wsu_dropshipper/dropshipper');
                 
                $model->setId($this->getRequest()->getParam('id'))
                    ->delete();
                     
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    public function massDeleteAction() {
        $dropshipperIds = $this->getRequest()->getParam('dropshipper');
        if(!is_array($dropshipperIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select item(s)'));
        } else {
            try {
                foreach ($dropshipperIds as $dropshipperId) {
                    $dropshipper = Mage::getModel('wsu_dropshipper/dropshipper')->load($dropshipperId);
                    $dropshipper->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d item(s) were successfully deleted', count($dropshipperIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function massStatusAction()
    {
        $dropshipperIds = $this->getRequest()->getParam('dropshipper');
        if(!is_array($dropshipperIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                foreach ($dropshipperIds as $dropshipperId) {
                    $dropshipper = Mage::getSingleton('wsu_dropshipper/dropshipper')
                        ->load($dropshipperId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d dropshipper(s) were successfully updated', count($dropshipperIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    public function massUpdatePriceAction()
    {
        $dropshipperIds = $this->getRequest()->getParam('dropshipper');
        $percentage = $this->getRequest()->getParam('percent');
        
        if(!is_array($dropshipperIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select item(s)'));
        } else {
            try {
                if($percentage && is_numeric($percentage)==true && is_integer($percentage)==false)
                {
                    foreach ($dropshipperIds as $dropshipperId) {
                        Mage::getModel('wsu_dropshipper/product')->updateProductPrice($dropshipperId,$percentage);
                    }
                    $this->_getSession()->addSuccess(
                        $this->__('Total of %d dropshipper(s) were successfully updated', count($dropshipperIds))
                    );
                }
                else
                {
                    $this->_getSession()->addError(
                        $this->__('Please add percentage')
                    );
                }
                
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
  
    public function exportCsvAction()
    {
        $fileName   = 'dropshipper.csv';
        $content    = $this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_grid')
            ->getCsv();

        $this->_sendUploadResponse($fileName, $content);
    }

    public function exportXmlAction()
    {
        $fileName   = 'dropshipper.xml';
        $content    = $this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_grid')
            ->getXml();

        $this->_sendUploadResponse($fileName, $content);
    }

    protected function _sendUploadResponse($fileName, $content, $contentType='application/octet-stream')
    {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK','');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename='.$fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
    protected function validate($data)
    {
        $errors = array();
        $helper = Mage::helper('wsu_dropshipper');
  
        if (!Zend_Validate::is( trim($data['name']) , 'NotEmpty')) {
            $errors[] = $helper->__('Name cannot be empty.');
        }
        if (!Zend_Validate::is( trim($data['email']) , 'NotEmpty')) {
            $errors[] = $helper->__('Email cannot be empty.');
        }
       
        return $errors;
    }
    
    /**
     * Grid Action
     * Display list of products related to document
     *
     * @return void
     */
    public function productGridAction()
    {
        $this->_initDropshipper();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('wsu_dropshipper/adminhtml_dropshipper_edit_tab_product', 'dropshipper.product.grid')
                ->toHtml()
        );
    }
    
    protected function _decodeInput($encoded)
    {
        $data = array();
        parse_str($encoded, $data);
        foreach($data as $key=>$value) {
            parse_str(base64_decode($value), $data[$key]);
        }
        return $data;
    }
}
    
