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

        $this->addColumn('code', array(
            'header' => Mage::helper('adminhtml')->__('Method Code'),
            'index' => 'code',
            'width' => 120,
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
            'width' => 140,
        ));

        $this->addColumn('stop_rules_processing', array(
            'header' => Mage::helper('mf_shippingrule')->__('Stop Further Rules Processing'),
            'index' => 'stop_rules_processing',
            'type' => 'options',
            'options'   => array(
                '1' => Mage::helper('adminhtml')->__('Yes'),
                '0' => Mage::helper('adminhtml')->__('No'),
            ),
            'width' => 140,
        ));

        $this->addColumn('sort_order', array(
            'header' => Mage::helper('adminhtml')->__('Sort Order'),
            'index' => 'sort_order',
            'width' => 100,
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
            'width' => 90,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('mf_shippingrule')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('mf_shippingrule')->__('XML'));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('rule_id');
        $this->getMassactionBlock()->setFormFieldName('rule_ids');
        $this->getMassactionBlock()->setUseSelectAll(true);

        $this->getMassactionBlock()->addItem('export_rule_csv', array(
            'label'=> Mage::helper('mf_shippingrule')->__('Export CSV'),
            'url'  => $this->getUrl('*/shippingrule/exportCsv'),
        ));

        $this->getMassactionBlock()->addItem('export_rule_xml', array(
            'label'=> Mage::helper('mf_shippingrule')->__('Export XML'),
            'url'  => $this->getUrl('*/shippingrule/exportXml'),
        ));

        $this->getMassactionBlock()->addItem('delete_rule', array(
            'label'=> Mage::helper('mf_shippingrule')->__('Delete'),
            'url'  => $this->getUrl('*/shippingrule/massDelete'),
        ));

        $this->getMassactionBlock()->addItem('enable_rule', array(
            'label'=> Mage::helper('mf_shippingrule')->__('Enable'),
            'url'  => $this->getUrl('*/shippingrule/massEnable'),
        ));

        $this->getMassactionBlock()->addItem('disable_rule', array(
            'label'=> Mage::helper('mf_shippingrule')->__('Disable'),
            'url'  => $this->getUrl('*/shippingrule/massDisable'),
        ));

        return $this;
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
