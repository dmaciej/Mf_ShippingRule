<?php

class Mf_ShippingRule_Model_Resource_Rule_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('mf_shippingrule/rule');
    }

    public function addStoreFilter($storeId = null, $withAdmin = true)
    {
        if ($storeId === null) {
            $storeId = Mage::app()->getStore()->getStoreId();
        }

        $this->getSelect()->join(
                array('store_table' => $this->getTable('mf_shippingrule/rule_store')),
                'main_table.rule_id = store_table.rule_id',
                array()
            )
            ->where('store_table.store_id in (?)', ($withAdmin ? array(0, $storeId) : $storeId))
            ->group('main_table.rule_id');

        /*
         * Allow analytic functions usage
         */
        $this->_useAnalyticFunction = true;

        return $this;
    }

    public function addStoreData()
    {
        $ruleIds = $this->getColumnValues('rule_id');
        $ruleStores = array();

        if (!empty($ruleIds)) {
            $select = $this->getConnection()->select()
                ->from($this->getTable('mf_shippingrule/rule_store'))
                ->where('rule_id IN (?)', $ruleIds);
            $result = $this->getConnection()->fetchAll($select);

            foreach ($result as $row) {
                if (!isset($ruleStores[$row['rule_id']])) {
                    $ruleStores[$row['rule_id']] = array();
                }
                $ruleStores[$row['rule_id']][] = $row['store_id'];
            }
        }

        foreach ($this as $item) {
            if (isset($ruleStores[$item->getId()])) {
                $item->setStores($ruleStores[$item->getId()]);
            } else {
                $item->setStores(array());
            }
        }

        return $this;
    }
}
