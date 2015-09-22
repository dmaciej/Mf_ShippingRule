<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Edit_Tab_Payments
    extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected function _prepareForm()
    {
        $model = Mage::registry('rule_data');
        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('payment', array(
            'legend' => Mage::helper('mf_shippingrule')->__('Payment Methods')
        ));

        $fieldset->addField('payment_method', 'multiselect', array(
            'label' => Mage::helper('mf_shippingrule')->__('Allowed Payment Methods'),
            'name' => 'payment_method',
            'values' => $this->_getPaymentMethodOptions(),
            'note'  => Mage::helper('mf_shippingrule')->__('Select payment methods to be available after choosing this shipping rule. No selection means all methods are available.'),
        ));

        $form->setValues($model->getData());
        $form->setFieldNameSuffix('rule');
        $this->setForm($form);

        return parent::_prepareForm();
    }

    public function getTabLabel()
    {
        return Mage::helper('mf_shippingrule')->__('Available payments');
    }

    public function getTabTitle()
    {
        return Mage::helper('mf_shippingrule')->__('Available payments');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    protected function _getPaymentMethodOptions()
    {
        $methods = Mage::getModel('payment/config')->getAllMethods();
        $options = array();
        foreach ($methods as $code => $method) {
            if (!$method->canUseCheckout()) {
                continue;
            }
            if ($method->isAvailable()) {
                $label = sprintf('%s (%s)', $method->getTitle(), Mage::helper('mf_shippingrule')->__('Available'));
            } else {
                $method->getTitle();
            }
            $options[] = array(
                'value' => $code,
                'label' => $label,
            );
        }
        return $options;
    }
}