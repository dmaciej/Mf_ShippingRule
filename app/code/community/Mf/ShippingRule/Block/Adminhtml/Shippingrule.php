<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'adminhtml_shippingrule';
        $this->_blockGroup = 'mf_shippingrule';
        $this->_headerText = Mage::helper('mf_shippingrule')->__('Manage Rules');
        $this->_addButtonLabel = Mage::helper('mf_shippingrule')->__('Add New Rule');

        parent::__construct();
    }

    public function getHeaderCssClass()
    {
        return 'head-shipping-method '.parent::getHeaderCssClass();
    }
}
