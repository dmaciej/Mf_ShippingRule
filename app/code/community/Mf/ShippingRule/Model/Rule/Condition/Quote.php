<?php

class Mf_ShippingRule_Model_Rule_Condition_Quote extends Mage_Rule_Model_Condition_Abstract
{
    public function loadAttributeOptions()
    {
        $attributes = array(
            'date' => Mage::helper('adminhtml')->__('Date'),
            'customer_group' => Mage::helper('customer')->__('Customer Group'),
            'base_subtotal' => Mage::helper('salesrule')->__('Subtotal'),
            'total_qty' => Mage::helper('salesrule')->__('Total Items Quantity'),
            'weight' => Mage::helper('salesrule')->__('Total Weight'),
            'postcode' => Mage::helper('salesrule')->__('Shipping Postcode'),
            'region' => Mage::helper('salesrule')->__('Shipping Region'),
            'region_id' => Mage::helper('salesrule')->__('Shipping State/Province'),
            'country_id' => Mage::helper('salesrule')->__('Shipping Country'),
        );

        $this->setAttributeOption($attributes);

        return $this;
    }

    public function getAttributeElement()
    {
        $element = parent::getAttributeElement();
        $element->setShowAsText(true);
        
        return $element;
    }

    public function getInputType()
    {
        switch ($this->getAttribute()) {
            case 'base_subtotal': 
            case 'weight': 
            case 'total_qty':
                return 'numeric';
                
            case 'date':
                return 'date';

            case 'customer_group':
                return 'multiselect';
                
            case 'country_id': 
            case 'region_id':
                return 'select';
                
            default:
                return 'string';
        }
    }

    public function getValueElementType()
    {
        switch ($this->getAttribute()) {
            case 'customer_group':
                return 'multiselect';

            case 'country_id': 
            case 'region_id':
                return 'select';
                
            case 'date':
                return 'date';
                
            default:
                return 'text';
        }
    }

    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'customer_group':
                    $options = Mage::getResourceModel('customer/group_collection')
                        ->toOptionArray();
                    break;
                    
                case 'country_id':
                    $options = Mage::getModel('adminhtml/system_config_source_country')
                        ->toOptionArray();
                    break;

                case 'region_id':
                    $options = Mage::getModel('adminhtml/system_config_source_allregion')
                        ->toOptionArray();
                    break;

                default:
                    $options = array();
            }
            $this->setData('value_select_options', $options);
        }
        
        return $this->getData('value_select_options');
    }
}
