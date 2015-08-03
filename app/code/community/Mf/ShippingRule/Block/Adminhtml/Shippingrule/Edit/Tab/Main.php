<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Edit_Tab_Main
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

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('mf_shippingrule')->__('Name'),
            'required' => true,
            'name' => 'name',
        ));
        
        $fieldset->addField('price', 'text', array(
            'label' => Mage::helper('mf_shippingrule')->__('Price'),
            'required' => true,
            'name' => 'price',
        ));

        $fieldset->addField('price_calculation_method', 'select', array(
            'label' => Mage::helper('mf_shippingrule')->__('Price Calculation Method'),
            'required' => true,
            'name' => 'price_calculation_method',
            'options' => Mage::getSingleton('mf_shippingrule/rule_price_calculation')->getOptionArray(),
        ));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => Mage::helper('mf_shippingrule')->__('Stop Further Rules Processing'),
            'title'     => Mage::helper('mf_shippingrule')->__('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'options'   => array(
                '1' => Mage::helper('adminhtml')->__('Yes'),
                '0' => Mage::helper('adminhtml')->__('No'),
            ),
            'note'      => Mage::helper('mf_shippingrule')->__('Skip checking the following rules when it passed.')
        ));

        $fieldset->addField('is_active', 'select', array(
            'label' => Mage::helper('adminhtml')->__('Status'),
            'name' => 'is_active',
            'required' => true,
            'options' => array(
                '1' => Mage::helper('adminhtml')->__('Active'),
                '0' => Mage::helper('adminhtml')->__('Inactive'),
            ),
        ));
        if (!$model->getId()) {
            $model->setData('is_active', '1');
        }
        
        $fieldset->addField('sort_order', 'text', array(
            'name'  => 'sort_order',
            'label' => Mage::helper('mf_shippingrule')->__('Sort Order'),
            'note'  => Mage::helper('mf_shippingrule')->__('Sort order is important in the case of enabled stop further rules processing option. Lower values will be checked first.'),
        ));
        if (!$model->getId()) {
            $model->setData('sort_order', '1000');
        }

        $model->getConditions()->setJsFormObject('rule_conditions_fieldset');

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
        $form->setFieldNameSuffix('rule');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('mf_shippingrule')->__('Rule Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('mf_shippingrule')->__('Rule Information');
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
