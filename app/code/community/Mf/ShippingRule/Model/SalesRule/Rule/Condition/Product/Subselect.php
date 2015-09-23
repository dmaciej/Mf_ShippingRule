<?php

class Mf_ShippingRule_Model_SalesRule_Rule_Condition_Product_Subselect
    extends Mage_SalesRule_Model_Rule_Condition_Product_Subselect
{
    public function validate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return false;
        }

        $attr = $this->getAttribute();
        $total = 0;
        foreach ($object->getQuote()->getAllVisibleItems() as $item) {
            if ($this->_validateItem($item)) {
                $total += $item->getData($attr);
            }
        }

        return $this->validateAttribute($total);
    }

    protected function _validateItem(Varien_Object $object)
    {
        /** @var Mage_Catalog_Model_Product $product */
        $product = $object->getProduct();
        if (!($product instanceof Mage_Catalog_Model_Product)) {
            $product = Mage::getModel('catalog/product')->load($object->getProductId());
        }

        $valid = $this->_ruleValidate($object);
        if (!$valid && $product->getTypeId() == Mage_Catalog_Model_Product_Type_Configurable::TYPE_CODE) {
            $children = $object->getChildren();
            $valid = $children && $this->validate($children[0]);
        }

        return $valid;
    }

    protected function _ruleValidate(Varien_Object $object)
    {
        if (!$this->getConditions()) {
            return true;
        }

        $all = $this->getAggregator() === 'all';

        foreach ($this->getConditions() as $cond) {
            $validated = $cond->validate($object);
            if ($all && !$validated) {
                return false;
            } elseif (!$all && $validated) {
                return true;
            }
        }
        return $all ? true : false;
    }
}
