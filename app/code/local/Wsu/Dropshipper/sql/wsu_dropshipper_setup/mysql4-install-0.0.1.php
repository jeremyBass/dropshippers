<?php
/**
 * Magento
 *
 * @category    Wsu
 * @package     Wsu_Dropshipper
*/
$installer = $this;
$installer->startSetup();

/**
 * Create table wsu_dropshipper/dropshipper
 */
$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('wsu_dropshipper/dropshipper')}`;");
$table = $installer->getConnection()
    ->newTable($installer->getTable('wsu_dropshipper/dropshipper'))
		->addColumn('dshipper_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'identity'  => true,
			'unsigned'  => true,
			'nullable'  => false,
			'primary'   => true,
        ), 'Auto Increment ID')
		->addColumn('name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Name')
		->addColumn('email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Help Email')
		->addColumn('contact_email', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Contact Email')
		->addColumn('contact_name', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Contact Name')
		
		//->addColumn('ds_icon',
		
		
		->addColumn('address', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Address')
		->addColumn('city', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'City')
		->addColumn('state', Varien_Db_Ddl_Table::TYPE_VARCHAR, 50, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'State')
        ->addColumn('country', Varien_Db_Ddl_Table::TYPE_VARCHAR, 10, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Country')
         ->addColumn('zip', Varien_Db_Ddl_Table::TYPE_VARCHAR, 20, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Zip')
        ->addColumn('status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'unsigned'  => true,
			'nullable'  => false,
			'default'   => '2',
        ), 'Status')
        ->addColumn('account_id', Varien_Db_Ddl_Table::TYPE_VARCHAR, 255, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Account ID')
    ->setComment('Dropshipper Main Table');
    
$installer->getConnection()->createTable($table);

/**
 * Create table wsu_dropshipper/product
 */
$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('wsu_dropshipper/product')}`;"); 
$table = $installer->getConnection()
    ->newTable($installer->getTable('wsu_dropshipper/product'))
		->addColumn('dshipper_product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
			'identity'  => true,
			'unsigned'  => true,
			'nullable'  => false,
			'primary'   => true,
        ), 'Auto Increment ID')
		->addColumn('product_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Product Id')
		->addColumn('cost', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
			'nullable'  => false,
			'default'   => '0.0000',
        ), 'Cost')
		->addColumn('price', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
			'nullable'  => false,
			'default'   => '0.0000',
        ), 'Price')
		->addColumn('qty', Varien_Db_Ddl_Table::TYPE_DECIMAL, '12,4', array(
			'nullable'  => false,
			'default'   => '0.0000',
        ), 'QTY')
		->addColumn('sku', Varien_Db_Ddl_Table::TYPE_TEXT, 64, array(
			'nullable'  => true,
        ), 'SKU')
		->addColumn('dshipper_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Reference of Dropshipper ID')
		->addIndex($installer->getIdxName('catalog/product', array('sku')),
			array('sku'))
		->addIndex(
        'UNIQUE_PRODUCT_DROPSHIPPER',
        array('product_id', 'dshipper_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
		->addForeignKey(
			'FK_PRODUCT_DROPSHIPPER_ID',
			'dshipper_id', $installer->getTable('wsu_dropshipper/dropshipper'), 'dshipper_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->addForeignKey(
			'FK_CATALOG_PRODUCT_DROPSHIPPER',
			'product_id', $installer->getTable('catalog/product'), 'entity_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('DROPSHIPPER Product Table');
    
$installer->getConnection()->createTable($table);

/**
 * Create table wsu_dropshipper/order
 */
$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('wsu_dropshipper/order')}`;"); 
$table = $installer->getConnection()
    ->newTable($installer->getTable('wsu_dropshipper/order'))
		->addColumn('dshipper_item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
			'identity'  => true,
			'unsigned'  => true,
			'nullable'  => false,
			'primary'   => true,
        ), 'Auto Increment ID')
		->addColumn('item_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Item Id')
		->addColumn('dshipper_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Dropshipper Id')
		->addColumn('mail_status', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Mail Status')
		->addIndex(
        'UNIQUE_ORDER_DROPSHIPPER',
        array('item_id', 'dshipper_id'),
        array('type' => Varien_Db_Adapter_Interface::INDEX_TYPE_UNIQUE))
		->addForeignKey(
			'FK_ORDER_DROPSHIPPER_ID',
			'dshipper_id', $installer->getTable('wsu_dropshipper/dropshipper'), 'dshipper_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
		->addForeignKey(
			'FK_SALES_ORDER_ITEM_DROPSHIPPER',
			'item_id', $installer->getTable('sales/order_item'), 'item_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Dropshipper order Table');
$installer->run("DROP TABLE IF EXISTS `{$installer->getTable('wsu_dropshipper/shipping')}`;"); 	
$table = $installer->getConnection()
    ->newTable($installer->getTable('wsu_dropshipper/shipping'))
		->addColumn('dshipper_shipping_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
			'identity'  => true,
			'unsigned'  => true,
			'nullable'  => false,
			'primary'   => true,
        ), 'Auto Increment ID')
		->addColumn('shipping_id', Varien_Db_Ddl_Table::TYPE_INTEGER, 10, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Item Id')
		->addColumn('dshipper_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
			'unsigned'  => true,
			'nullable'  => false,
        ), 'Dropshipper Id')
    ->setComment('Dropshipper shipping method Table');
	
	
$installer->getConnection()->createTable($table);

$installer->endSetup();

