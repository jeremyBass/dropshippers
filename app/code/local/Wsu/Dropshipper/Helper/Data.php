<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
class Wsu_Dropshipper_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function isActiveDropshipper()
    {
        return Mage::getStoreConfig('dropshipper/general/enabled');
    }
}