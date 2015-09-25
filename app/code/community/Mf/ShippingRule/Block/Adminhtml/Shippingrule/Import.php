<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Import
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->removeButton('back')
            ->removeButton('reset')
            ->_updateButton('save', 'label', $this->__('Import'))
            ->_updateButton('save', 'id', 'upload_button');
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_blockGroup = 'mf_shippingrule';
        $this->_controller = 'adminhtml_shippingrule_import';
    }

    public function getHeaderText()
    {
        return Mage::helper('mf_shippingrule')->__('Import');
    }
}
