<?php

class Mf_ShippingRule_Model_Rule_Condition_Quote extends Mage_Rule_Model_Condition_Abstract
{
    /**
     * Default operator input by type map getter
     *
     * @return array
     */
    public function getDefaultOperatorInputByType()
    {
        $this->_defaultOperatorInputByType = parent::getDefaultOperatorInputByType();
        $this->_defaultOperatorInputByType['time'] = array('==', '>=', '<=', '<', '>');

        return $this->_defaultOperatorInputByType;
    }

    public function loadAttributeOptions()
    {
        $attributes = array(
            'day_of_week' => Mage::helper('mf_shippingrule')->__('Day of Week'),
            'time' => Mage::helper('mf_shippingrule')->__('Order Time'),
            'date' => Mage::helper('mf_shippingrule')->__('Order Date'),
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

            case 'time':
                return 'time';

            case 'day_of_week':
                return 'grid';

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
            case 'day_of_week':
            case 'customer_group':
                return 'multiselect';

            case 'country_id':
            case 'region_id':
                return 'select';

            case 'date':
                return 'date';

            case 'time':
                return 'text';

            default:
                return 'text';
        }
    }

    public function getValueSelectOptions()
    {
        if (!$this->hasData('value_select_options')) {
            switch ($this->getAttribute()) {
                case 'day_of_week':
                    $options = Mage::getSingleton('adminhtml/system_config_source_locale_weekdays')
                        ->toOptionArray();
                    break;

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

    /**
     * Validate product attrbute value for condition
     *
     * @param   mixed $validatedValue product attribute value
     * @return  bool
     */
    public function validateAttribute($validatedValue)
    {
        if (is_object($validatedValue)) {
            return false;
        }

        /**
         * Condition attribute value
         */
        $value = $this->getValueParsed();

        /**
         * Comparison operator
         */
        $op = $this->getOperatorForValidate();

        // if operator requires array and it is not, or on opposite, return false
        if ($this->isArrayOperatorType() xor is_array($value)) {
            return false;
        }

        $result = false;

        if ($this->getInputType() == 'time') {
            list($valueHour, $valueMinutes) = $this->_parseTimeValue($value);
            list($validatedValueHour, $validatedValueMinutes) = $this->_parseTimeValue($validatedValue);

            switch ($op) {
                case '==':
                case '!=':
                    $result = $this->_compareValues($validatedValueHour, $valueHour)
                        && $this->_compareValues($validatedValueMinutes, $valueMinutes);
                    break;

                case '<=':
                case '>':
                    if ($validatedValueHour < $valueHour) {
                        $result = true;
                    } elseif ($validatedValueHour == $valueHour) {
                        $result = $validatedValueMinutes <= $valueMinutes;
                    } else {
                        $result = false;
                    }
                    break;

                case '>=':
                case '<':
                    if ($validatedValueHour < $valueHour) {
                        $result = false;
                    } elseif ($validatedValueHour == $valueHour) {
                        $result = $validatedValueMinutes >= $valueMinutes;
                    } else {
                        $result = true;
                    }
                    break;
            }

            if ('!=' == $op || '>' == $op || '<' == $op) {
                $result = !$result;
            }

            return $result;
        } else {
            return parent::validateAttribute($validatedValue);
        }
    }

    /**
     * @param string $value
     * @return array
     */
    protected function _parseTimeValue($value)
    {
        if (strpos($value, ':') === false) {
            $hour = (int) $value;
            $minutes = (int) '00';
        } else {
            list($hour, $minutes) = explode(':', $value);
            $hour = (int) $hour;
            $minutes = (int) $minutes;
        }

        return array($hour, $minutes);
    }
}