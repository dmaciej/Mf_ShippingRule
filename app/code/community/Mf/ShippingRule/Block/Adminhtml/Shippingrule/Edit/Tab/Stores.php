<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Edit_Tab_Stores
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('rule_data');
        $form = new Varien_Data_Form();
        
        $fieldset = $form->addFieldset('store_information', array(
            'legend' => Mage::helper('mf_shippingrule')->__('Store Information'),
            'class' => 'fieldset-wide',
        ));

        if (!Mage::app()->isSingleStoreMode()) {
            if (!$model->getStoreIds()) {
                $model->setStoreIds(0);
            }
            $fieldset->addField('store_ids', 'multiselect', array(
                'label'     => Mage::helper('mf_shippingrule')->__('Available In'),
                'required'  => true,
                'name'      => 'store_ids[]',
                'values'    => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
                'value'     => $model->getStoreIds(),
                'after_element_html' => Mage::getBlockSingleton('adminhtml/store_switcher')->getHintHtml()
            ));
        }
        else {
            $fieldset->addField('store_ids', 'hidden', array(
                'name'      => 'store_ids[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreIds(Mage::app()->getStore(true)->getId());
        }

        $form->setValues($model->getData());
        $form->setFieldNameSuffix('rule');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('mf_shippingrule')->__('Stores');
    }

    public function getTabTitle()
    {
        return Mage::helper('mf_shippingrule')->__('Stores');
    }

    public function canShowTab()
    {
        return !Mage::app()->isSingleStoreMode();
    }

    public function isHidden()
    {
        return Mage::app()->isSingleStoreMode();
    }
}