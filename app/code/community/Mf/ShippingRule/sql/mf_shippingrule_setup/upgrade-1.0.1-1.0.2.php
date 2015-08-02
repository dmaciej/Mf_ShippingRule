<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Add payment methods
 */
$table = $installer->getConnection()
    ->addColumn(
        $installer->getTable('mf_shippingrule/rule'),
        'payment_method',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'comment' => 'Payment methods'
        )
    );

$installer->endSetup();
