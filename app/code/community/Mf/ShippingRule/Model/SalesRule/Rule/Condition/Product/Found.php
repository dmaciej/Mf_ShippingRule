<?php

class Mf_ShippingRule_Model_SalesRule_Rule_Condition_Product_Found
    extends Mage_SalesRule_Model_Rule_Condition_Product_Found
{
    protected function _compareValues($validatedValue, $value, $strict = true)
    {
        if ($validatedValue === null) {
            $validatedValue = 0;
        }
        return parent::_compareValues($validatedValue, $value, $strict);
    }
}
