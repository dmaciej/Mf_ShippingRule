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
    
    public function prepareRate(Mage_Shipping_Model_Carrier_Abstract $carrier, Mage_Shipping_Model_Rate_Request $request)
    {
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($carrier->getCarrierCode());
        $rate->setCarrierTitle($carrier->getConfigData('name'));
        $rate->setMethod($this->getId());
        $rate->setMethodTitle($this->getName());

        if ($request->getFreeShipping() === true) {
            $price = '0.00';
        } else {
            switch ($this->getPriceCalculationMethod()) {
                case Mf_ShippingRule_Model_Rule_Price_Calculation::METHOD_ITEM_QUANTITY:
                    $price = $this->getPrice() * $request->getPackageQty();
                    break;

                case Mf_ShippingRule_Model_Rule_Price_Calculation::METHOD_WEIGHT_UNIT:
                    $price = $this->getPrice() * $request->getPackageWeight();
                    break;

                case Mf_ShippingRule_Model_Rule_Price_Calculation::METHOD_ORDER:
                default:
                    $price = $this->getPrice();
                    break;
            }
        }
        $rate->setPrice($price);
        $rate->setCost(0);
        
        $params = array('rate' => $rate, 'rule' => $this, 'request' => $request);
        Mage::dispatchEvent('mf_shippingrule_prepare_rate', $params);

        return $rate;
    }
}
