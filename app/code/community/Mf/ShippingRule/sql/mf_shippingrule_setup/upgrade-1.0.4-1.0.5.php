<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Add price calculation method.
 */
$table = $installer->getConnection()
    ->addColumn(
        $installer->getTable('mf_shippingrule/rule'),
        'code',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'comment' => 'Method Code',
        )
    );

$installer->endSetup();