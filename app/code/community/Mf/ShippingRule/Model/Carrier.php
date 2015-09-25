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
            ->setOrder('sort_order', 'asc');

        $customer = Mage::getSingleton('customer/session');

        $object = clone $request;
        $object->addData(array(
            'day_of_week' => Mage::getModel('core/date')->date('N'),
            'time' => Mage::getModel('core/date')->date('H:i'),
            'date' => Mage::getModel('core/date')->date('Y-m-d'),
            'customer_group' => $customer->getCustomerGroupId(),
            'base_subtotal' => $request->getPackageValue(),
            'total_qty' => $request->getPackageQty(),
            'weight' => $request->getPackageWeight(),
            'postcode' => $request->getDestPostcode(),
            'region' => $request->getDestRegionCode(),
            'region_id' => $request->getDestRegionId(),
            'country_id' => $request->getDestCountryId(),
            'quote' => Mage::getSingleton('checkout/session')->getQuote(),
        ));

        foreach ($rules as $rule) {
            $validation = new Varien_Object();
            $validation->setValue($rule->getConditions()->validate($object));
            $params = array('rule' => $rule, 'request' => $request, 'validation' => $validation);
            Mage::dispatchEvent('mf_shippingrule_rate_validate', $params);

            if ($validation->getValue()) {
                $result->append($rule->prepareRate($this, $request));
                if ($rule->getStopRulesProcessing()) {
                    break;
                }
            }
        }

        $request->setPackageWeight($oldWeight);
        $request->setPackageQty($oldQty);

        return $result;
    }

    public function getAllowedMethods()
    {
        return array('mf_shippingrule' => $this->getConfigData('name'));
    }
}