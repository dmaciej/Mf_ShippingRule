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
    
    public function getPriceCalculationModel()
    {
        return Mage::getModel('mf_shippingrule/rule_price_calculation');
    }
    
    public function prepareRate(Mage_Shipping_Model_Carrier_Abstract $carrier, Mage_Shipping_Model_Rate_Request $request)
    {
        /* @var $rate Mage_Shipping_Model_Rate_Result_Method */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($carrier->getCarrierCode());
        $rate->setCarrierTitle($carrier->getConfigData('name'));
        $rate->setMethod($this->getId());
        $rate->setMethodTitle($this->getFrontendName());
        $rate->setMethodDescription($this->getDescription());

        $price = $this->getPriceCalculationModel()
            ->calculatePrice($this->getPrice(), $this->getPriceCalculationMethod(), $request);
        $rate->setPrice($price);
        $rate->setCost(0);
        
        $params = array('rate' => $rate, 'rule' => $this, 'request' => $request);
        Mage::dispatchEvent('mf_shippingrule_prepare_rate', $params);

        return $rate;
    }

    public function addStoreId($storeId)
    {
        $ids = $this->getStoreIds();
        if (!in_array($storeId, $ids)) {
            $ids[] = $storeId;
        }
        $this->setStoreIds($ids);
        return $this;
    }

    public function getStoreIds()
    {
        $ids = $this->_getData('store_ids');
        if (is_null($ids)) {
            $this->_getResource()->loadStoreIds($this);
            $ids = $this->getData('store_ids');
        }
        return $ids;
    }
}