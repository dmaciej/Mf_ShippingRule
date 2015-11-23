<?php

class Mf_ShippingRule_Model_Rule_Condition_Combine extends Mage_Rule_Model_Condition_Combine
{
    public function __construct()
    {
        parent::__construct();
        $this->setType('mf_shippingrule/rule_condition_combine');
    }

    public function getNewChildSelectOptions()
    {
        $addressCondition = Mage::getModel('mf_shippingrule/rule_condition_quote');
        $addressAttributes = $addressCondition->loadAttributeOptions()->getAttributeOption();
        $attributes = array();
        foreach ($addressAttributes as $code=>$label) {
            $attributes[] = array('value'=>'mf_shippingrule/rule_condition_quote|'.$code, 'label'=>$label);
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive($conditions, array(
            array('value'=>'salesrule/rule_condition_product_found', 'label'=>Mage::helper('salesrule')->__('Product attribute combination')),
            array('value'=>'salesrule/rule_condition_product_subselect', 'label'=>Mage::helper('salesrule')->__('Products subselection')),
            array('value'=>'mf_shippingrule/rule_condition_combine', 'label'=>Mage::helper('salesrule')->__('Conditions combination')),
            array('label'=>Mage::helper('salesrule')->__('Cart Attribute'), 'value'=>$attributes),
        ));

        return $conditions;
    }
}
