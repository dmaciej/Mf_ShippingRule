<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();

        $this->setId('shippingrule_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('mf_shippingrule')->__('Shipping Rule'));
    }
}
