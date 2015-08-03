<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Grid
    extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('shippingrule_grid');
        $this->setDefaultSort('rule_id');
        $this->setDefaultDir('DESC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection()
    {
        $collection = Mage::getModel('mf_shippingrule/rule')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        $this->addColumn('rule_id', array(
            'header' => Mage::helper('adminhtml')->__('ID'),
            'align' => 'right',
            'width' => 50,
            'type' => 'number',
            'index' => 'rule_id',
        ));

        $this->addColumn('name', array(
            'header' => Mage::helper('adminhtml')->__('Name'),
            'index' => 'name',
        ));

        $this->addColumn('price', array(
            'header' => Mage::helper('adminhtml')->__('Price'),
            'index' => 'price',
            'width' => 120,
            'type' => 'currency',
        ));

        $this->addColumn('price_calculation_method', array(
            'header' => Mage::helper('adminhtml')->__('Price Calculation Method'),
            'index' => 'price_calculation_method',
            'type' => 'options',
            'options' => Mage::getSingleton('mf_shippingrule/rule_price_calculation')->getOptionArray(),
            'width' => 160,
        ));

        $this->addColumn('sort_order', array(
            'header' => Mage::helper('catalog')->__('Sort Order'),
            'index' => 'sort_order',
            'width' => 120,
            'type' => 'number',
        ));

        $this->addColumn('is_active', array(
            'header' => Mage::helper('adminhtml')->__('Status'),
            'index' => 'is_active',
            'type' => 'options',
            'options' => array(
                1 => Mage::helper('adminhtml')->__('Enabled'),
                0 => Mage::helper('adminhtml')->__('Disabled'),
            ),
            'width' => 120,
        ));

        return parent::_prepareColumns();
    }

    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
}
