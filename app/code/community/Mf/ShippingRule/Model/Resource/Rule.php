<?php

class Mf_ShippingRule_Model_Resource_Rule extends Mage_Core_Model_Resource_Db_Abstract
{
    protected $_serializableFields   = array(
        'payment_method' => array(null, array())
    );

    protected function _construct()
    {
        $this->_init('mf_shippingrule/rule', 'rule_id');
    }

    protected function _afterSave(Mage_Core_Model_Abstract $object)
    {
        // Save product attributes used in rule
        $ruleProductAttributes = $this->getProductAttributes(serialize($object->getConditions()->asArray()));
        if (count($ruleProductAttributes)) {
            $this->setActualProductAttributes($object, $ruleProductAttributes);
        }

        return parent::_afterSave($object);
    }

    public function getActiveAttributes()
    {
        $read = $this->_getReadAdapter();
        $select = $read->select()
            ->from(array('a' => $this->getTable('mf_shippingrule/product_attribute')),
                new Zend_Db_Expr('DISTINCT ea.attribute_code'))
            ->joinInner(array('ea' => $this->getTable('eav/attribute')), 'ea.attribute_id = a.attribute_id', array());
        return $read->fetchAll($select);
    }

    public function setActualProductAttributes($rule, $attributes)
    {
        $write = $this->_getWriteAdapter();
        $write->delete($this->getTable('mf_shippingrule/product_attribute'), array('rule_id=?' => $rule->getId()));

        //Getting attribute IDs for attribute codes
        $attributeIds = array();
        $select = $this->_getReadAdapter()->select()
            ->from(array('a' => $this->getTable('eav/attribute')), array('a.attribute_id'))
            ->where('a.attribute_code IN (?)', array($attributes));
        $attributesFound = $this->_getReadAdapter()->fetchAll($select);
        if ($attributesFound) {
            foreach ($attributesFound as $attribute) {
                $attributeIds[] = $attribute['attribute_id'];
            }

            $data = array();
            foreach ($attributeIds as $attribute) {
                $data[] = array (
                    'rule_id'           => $rule->getId(),
                    'attribute_id'      => $attribute
                );
            }
            $write->insertMultiple($this->getTable('mf_shippingrule/product_attribute'), $data);
        }

        return $this;
    }

    public function getProductAttributes($serializedString)
    {
        $result = array();
        if (preg_match_all('~s:32:"salesrule/rule_condition_product";s:9:"attribute";s:\d+:"(.*?)"~s',
            $serializedString, $matches)){
            foreach ($matches[1] as $offset => $attributeCode) {
                $result[] = $attributeCode;
            }
        }

        return $result;
    }
}
