<?php

/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;
$installer->startSetup();

/**
 * Add frontend name.
 */
$table = $installer->getConnection()
    ->addColumn(
        $installer->getTable('mf_shippingrule/rule'),
        'frontend_name',
        array(
            'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
            'nullable'  => false,
            'comment' => 'Frontend Name',
        )
    );

$installer->endSetup();
