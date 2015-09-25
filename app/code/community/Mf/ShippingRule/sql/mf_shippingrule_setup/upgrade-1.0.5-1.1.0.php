<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'rule_store'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mf_shippingrule/rule_store'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'primary'   => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Rule Id')
    ->addColumn('store_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'primary'   => true,
        'nullable'  => false,
        'default'   => '0',
    ), 'Store id')
    ->addIndex($installer->getIdxName('mf_shippingrule/rule_store', array('store_id')),
        array('store_id'))
    ->addForeignKey($installer->getFkName('mf_shippingrule/rule_store', 'rule_id', 'mf_shippingrule/rule', 'rule_id'),
        'rule_id', $installer->getTable('mf_shippingrule/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->addForeignKey($installer->getFkName('mf_shippingrule/rule_store', 'store_id', 'core/store', 'store_id'),
        'store_id', $installer->getTable('core/store'), 'store_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_CASCADE)
    ->setComment('Rule Store');
$installer->getConnection()->createTable($table);

$installer->endSetup();
