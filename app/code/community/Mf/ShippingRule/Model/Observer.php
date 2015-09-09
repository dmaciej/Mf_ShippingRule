<?php

class Mf_ShippingRule_Model_Observer
{
    public function addProductAttributes(Varien_Event_Observer $observer)
    {
        $attributesTransfer = $observer->getEvent()->getAttributes();

        $attributes = Mage::getResourceModel('mf_shippingrule/rule')
            ->getActiveAttributes();
        $result = array();
        foreach ($attributes as $attribute) {
            $result[$attribute['attribute_code']] = true;
        }
        $attributesTransfer->addData($result);
        return $this;
    }

    public function validatePayments(Varien_Event_Observer $observer)
    {
        $quote = $observer->getEvent()->getQuote();
        if (!$quote) {
            return;
        }

        $methodInstance = $observer->getEvent()->getMethodInstance();
        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();

        $pos = strrpos($shippingMethod, '_');
        $code = substr($shippingMethod, 0, $pos);
        if (Mage::getSingleton('mf_shippingrule/carrier')->getCarrierCode() != $code) {
            return;
        }
        $ruleId = substr($shippingMethod, $pos + 1);

        $model = Mage::getModel('mf_shippingrule/rule');
        $model->load($ruleId);

        if (!in_array($methodInstance->getCode(), $model->getPaymentMethod())) {
            $observer->getEvent()->getResult()->isAvailable = false;
        }
    }
}
