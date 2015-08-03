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
        'price_calculation_method',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'length' => 32,
            'nullable'  => false,
            'comment' => 'Price Calculation Method'
        )
    );

$installer->endSetup();
