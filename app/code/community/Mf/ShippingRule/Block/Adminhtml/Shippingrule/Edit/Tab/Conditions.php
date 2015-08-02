<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Edit_Tab_Conditions
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('mf_shippingrule/rule')->load($id);
        $form = new Varien_Data_Form();
        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');
        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/*/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('mf_shippingrule')->__('Conditions'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('mf_shippingrule')->__('Conditions'),
            'title' => Mage::helper('mf_shippingrule')->__('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('rule/conditions'));

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('mf_shippingrule')->__('Conditions');
    }

    public function getTabTitle()
    {
        return Mage::helper('mf_shippingrule')->__('Conditions');
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
