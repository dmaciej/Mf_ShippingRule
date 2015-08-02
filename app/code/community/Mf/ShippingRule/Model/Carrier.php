<?php

class Mf_ShippingRule_Model_Carrier
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    protected $_code = 'mf_shippingrule';

    public function collectRates(
        Mage_Shipping_Model_Rate_Request $request
    )
    {
        if (!$this->getConfigFlag('active')) {
            return false;
        }

        /* @var $result Mage_Shipping_Model_Rate_Result */
        $result = Mage::getModel('shipping/rate_result');

        $rules = Mage::getModel('mf_shippingrule/rule')->getCollection()
            ->addFieldToFilter('is_active', array('eq' => true))
            ->setOrder('sort_order');

        $customer = Mage::getSingleton('customer/session');

        $object = clone $request;
        $object->addData(array(
            'date' => Mage::getModel('core/date')->date('Y-m-d'),
            'customer_group' => $customer->getCustomerGroupId(),
            'base_subtotal' => $request->getPackageValue(),
            'total_qty' => $request->getPackageQty(),
            'weight' => $request->getPackageWeight(),
            'postcode' => $request->getDestPostcode(),
            'region' => $request->getDestRegionCode(),
            'region_id' => $request->getDestRegionId(),
            'country_id' => $request->getDestCountryId(),
        ));

        foreach ($rules as $rule) {
            if ($rule->getConditions()->validate($object)) {
                $result->append($this->_getShippingRate($rule));
                if ($rule->getStopRulesProcessing()) {
                    break;
                }
            }
        }

        return $result;
    }

    protected function _getShippingRate(Mf_ShippingRule_Model_Rule $rule)
    {
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);

        $rate->setCarrierTitle($this->getConfigData('name'));
        $rate->setMethod($rule->getId());
        $rate->setMethodTitle($rule->getName());
        $rate->setPrice($rule->getPrice());
        $rate->setCost(0);

        return $rate;
    }

    public function getAllowedMethods()
    {
        return array('mf_shippingrule' => $this->getConfigData('name'));
    }
}
