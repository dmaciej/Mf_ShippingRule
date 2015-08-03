<?php

class Mf_ShippingRule_Model_Rule extends Mage_SalesRule_Model_Rule
{
    protected function _construct()
    {
        $this->_init('mf_shippingrule/rule');
    }

    public function isDuplicable()
    {
        return (bool) $this->getId();
    }

    public function getConditionsInstance()
    {
        return Mage::getModel('mf_shippingrule/rule_condition_combine');
    }
}
