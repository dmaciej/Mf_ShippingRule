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
            'legend' => Mage::helper('mf_shippingrule')->__('Shipping Rule')
        ));

        $fieldset->addField('name', 'text', array(
            'label' => Mage::helper('mf_shippingrule')->__('Method Name'),
            'required' => true,
            'name' => 'name',
        ));
        
        $fieldset->addField('price', 'text', array(
            'label' => Mage::helper('mf_shippingrule')->__('Method Price'),
            'required' => true,
            'name' => 'price',
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

        $fieldset = $form->addFieldset('payment', array(
            'legend' => Mage::helper('mf_shippingrule')->__('Payment Methods')
        ));

        $fieldset->addField('payment_method', 'multiselect', array(
            'label' => Mage::helper('mf_shippingrule')->__('Allowed Payment Methods'),
            'name' => 'payment_method',
            'required' => true,
            'values' => $this->_getPaymentMethodOptions(),
            'note'  => Mage::helper('mf_shippingrule')->__('Select payment methods to be available after choosing this shipping method.'),
        ));

        $form->setValues($model->getData());
        $form->setFieldNameSuffix('rule');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    protected function _getPaymentMethodOptions()
    {
        $methods = Mage::getModel('payment/config')->getAllMethods();
        $options = array();
        foreach ($methods as $code => $method) {
            $options[] = array(
                'value' => $code,
                'label' => $method->getTitle(),
            );
        }
        return $options;
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
