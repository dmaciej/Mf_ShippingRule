<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Create table 'mf_shippingrule/product_attribute'
 */
$table = $installer->getConnection()
    ->newTable($installer->getTable('mf_shippingrule/product_attribute'))
    ->addColumn('rule_id', Varien_Db_Ddl_Table::TYPE_INTEGER, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Rule Id')
    ->addColumn('attribute_id', Varien_Db_Ddl_Table::TYPE_SMALLINT, null, array(
        'unsigned'  => true,
        'nullable'  => false,
        'primary'   => true,
    ), 'Attribute Id')
    ->addIndex($installer->getIdxName('salesrule/product_attribute', array('attribute_id')),
        array('attribute_id'))
    ->addForeignKey($installer->getFkName('mf_shippingrule/product_attribute', 'attribute_id', 'eav/attribute', 'attribute_id'),
        'attribute_id', $installer->getTable('eav/attribute'), 'attribute_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION)
    ->addForeignKey($installer->getFkName('mf_shippingrule/product_attribute', 'rule_id', 'mf_shippingrule/rule', 'rule_id'),
        'rule_id', $installer->getTable('mf_shippingrule/rule'), 'rule_id',
        Varien_Db_Ddl_Table::ACTION_CASCADE, Varien_Db_Ddl_Table::ACTION_NO_ACTION);
$installer->getConnection()->createTable($table);
