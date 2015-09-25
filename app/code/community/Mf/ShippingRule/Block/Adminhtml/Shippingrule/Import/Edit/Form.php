<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Import_Edit_Form
    extends Mage_Adminhtml_Block_Widget_Form
{
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/importPost'),
            'method'  => 'post',
            'enctype' => 'multipart/form-data'
        ));

        $fieldset = $form->addFieldset('import', array(
            'legend' => Mage::helper('mf_shippingrule')->__('Import Settings')
        ));

        $fieldset->addField('file', 'file', array(
            'name'     => 'file',
            'label'    => Mage::helper('mf_shippingrule')->__('Select File to Import'),
            'title'    => Mage::helper('mf_shippingrule')->__('Select File to Import'),
            'required' => true,
        ));

        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}