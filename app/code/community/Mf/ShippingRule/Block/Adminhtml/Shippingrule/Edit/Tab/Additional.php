<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Edit_Tab_Additional
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('rule_data');
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('rule_information', array(
            'legend' => Mage::helper('mf_shippingrule')->__('Rule')
        ));

        $fieldset->addField('code', 'text', array(
            'label' => Mage::helper('mf_shippingrule')->__('Method Code'),
            'name' => 'code',
        ));

        $fieldset->addField('description', 'textarea', array(
            'name'  => 'description',
            'label' => Mage::helper('mf_shippingrule')->__('Description'),
        ));

        $form->setValues($model->getData());
        $form->setFieldNameSuffix('rule');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('mf_shippingrule')->__('Additional Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('mf_shippingrule')->__('Additional Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
