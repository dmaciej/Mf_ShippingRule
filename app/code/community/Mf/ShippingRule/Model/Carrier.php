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

        $freeQty = 0;
        if ($request->getAllItems()) {
            foreach ($request->getAllItems() as $item) {
                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
                    continue;
                }

                if ($item->getHasChildren() && $item->isShipSeparately()) {
                    foreach ($item->getChildren() as $child) {
                        if ($child->getFreeShipping() && !$child->getProduct()->isVirtual()) {
                            $freeQty += $item->getQty() * $child->getQty();
                        }
                    }
                } elseif ($item->getFreeShipping()) {
                    $freeQty += $item->getQty();
                }
            }
        }

        // Package weight and qty free shipping
        $oldWeight = $request->getPackageWeight();
        $oldQty = $request->getPackageQty();

        $request->setPackageWeight($request->getFreeMethodWeight());
        $request->setPackageQty($oldQty - $freeQty);

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
                $result->append($this->_getShippingRate($rule, $request));
                if ($rule->getStopRulesProcessing()) {
                    break;
                }
            }
        }

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        return $result;
    }

    protected function _getShippingRate(Mf_ShippingRule_Model_Rule $rule, Mage_Shipping_Model_Rate_Request $request)
    {
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('name'));
        $rate->setMethod($rule->getId());
        $rate->setMethodTitle($rule->getName());

        switch ($rule->getPriceCalculationMethod()) {
            case Mf_ShippingRule_Model_Rule_Price_Calculation::METHOD_ITEM_QUANTITY:
                $price = $rule->getPrice() * $request->getPackageQty();
                break;

            case Mf_ShippingRule_Model_Rule_Price_Calculation::METHOD_WEIGHT_UNIT:
                $price = $rule->getPrice() * $request->getPackageWeight();
                break;

            case Mf_ShippingRule_Model_Rule_Price_Calculation::METHOD_ORDER:
            default:
                $price = $rule->getPrice();
                break;
        }
        $rate->setPrice($price);
        $rate->setCost(0);
        
        $params = array('rate' => $rate, 'rule' => $rule, 'request' => $request);
        Mage::dispatchEvent('mf_shippingrule_prepare_rate', $params);

        return $rate;
    }

    public function getAllowedMethods()
    {
        return array('mf_shippingrule' => $this->getConfigData('name'));
    }
}
