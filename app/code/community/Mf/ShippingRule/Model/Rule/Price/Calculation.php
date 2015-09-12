<?php

class Mf_ShippingRule_Model_Rule_Price_Calculation
    extends Mage_Core_Model_Abstract
{
    const METHOD_ITEM_QUANTITY = 'item_quantity';
    const METHOD_WEIGHT_UNIT = 'weight_unit';
    const METHOD_ORDER = 'order';

    public function getOptionArray()
    {
        return array(
            self::METHOD_ITEM_QUANTITY => Mage::helper('mf_shippingrule')->__('Single Item Quantity'),
            self::METHOD_WEIGHT_UNIT => Mage::helper('mf_shippingrule')->__('Weight Unit'),
            self::METHOD_ORDER => Mage::helper('mf_shippingrule')->__('Entire Order'),
        );
    }
    
    public function calculatePrice($price, $method, Mage_Shipping_Model_Rate_Request $request)
    {
        switch ($method) {
            case self::METHOD_ITEM_QUANTITY:
                return $price * $request->getPackageQty();
                break;

            case self::METHOD_WEIGHT_UNIT:
                return $price * $request->getPackageWeight();
                break;

            case self::METHOD_ORDER:
            default:
                return $price;
                break;
        }
    }
}
