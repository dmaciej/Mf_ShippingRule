<?php

class Mf_ShippingRule_Adminhtml_ShippingruleController
    extends Mage_Adminhtml_Controller_Action
{
    protected function _getHelper()
    {
        return Mage::helper('mf_shippingrule');
    }
    
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('mf_shippingrule')
            ->_addBreadcrumb(
                $this->_getHelper()->__('Shipping Rules'), 
                $this->_getHelper()->__('Shipping Rules')
            );
        return $this;
    }
    
    public function indexAction()
    {
        $this->_title($this->__('Shipping Rules'))->_title($this->__('Manage Rules'));

        if ($this->getRequest()->getQuery('ajax')) {
            $this->_forward('grid');
            return;
        }

        $this->_initAction()
            ->_addBreadcrumb(
                $this->_getHelper()->__('Manage Rules'), 
                $this->_getHelper()->__('Manage Rules')
            );

        $this->renderLayout();
    }

    public function gridAction()
    {
        $this->loadLayout();
        $this->renderLayout();
    }

    public function newAction()
    {
        $this->_forward('edit');
    }

    public function editAction()
    {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('mf_shippingrule/rule');
        
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->_getSession()->addError(
                    $this->_getHelper()->__('This rule no longer exists.')
                );
                $this->_redirect('*/*');
                return;
            }
        }
        $this->_title($id ? $model->getName() : $this->__('New Rule'));
        
        $data = $this->_getSession()->getFormData(true);
        if (!empty($data)) {
            $model->setData($data);
        } 
        Mage::register('rule_data', $model);
        
        $this->_initAction()
            ->_addBreadcrumb(
                $this->_getHelper()->__('Manage Rules'), 
                $this->_getHelper()->__('Manage Rules')
            )
            ->_addBreadcrumb(
                $id ? $this->_getHelper()->__('Edit Rule')
                    : $this->_getHelper()->__('New Rule'),
                $id ? $this->_getHelper()->__('Edit Rule')
                    : $this->_getHelper()->__('New Rule')
            );
        $this->renderLayout();   
    }

    public function saveAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $data = $this->getRequest()->getParam('rule', array());

        if ($data) {
            try {
                if (empty($data['payment_method'])) {
                    $data['payment_method'] = array();
                }

                $model = Mage::getModel('mf_shippingrule/rule');
                $model->load($id);
                $model->loadPost($data);
                $model->setData('payment_method', $data['payment_method']);
                $this->_getSession()->setFormData($data);

                $stores = isset($data['store_ids']) ? $data['store_ids'] : array();
                $storeIds = array();
                if (!is_array($stores) || count($stores) === 0) {
                    Mage::throwException(Mage::helper('mf_shippingrule')->__('Please, select "Available in Stores" for this rule first.'));
                }
                if (is_array($stores)) {
                    foreach ($stores as $storeIdList) {
                        $storeIdList = explode(',', $storeIdList);
                        if (count($storeIdList) === 0) {
                            continue;
                        }
                        foreach ($storeIdList as $storeId) {
                            if ($storeId >= 0) {
                                $storeIds[] = $storeId;
                            }
                        }
                    }
                    if (count($storeIds) === 0) {
                        Mage::throwException(Mage::helper('mf_shippingrule')->__('Please, select "Available in Stores" for this rule first.'));
                    }
                }
                $model->setStoreIds($storeIds);

                $model->save();
                $this->_getSession()->setFormData(false);
                $this->_getSession()->addSuccess(
                    $this->_getHelper()->__('Rule was successfully saved.')
                );
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array(
                        'id' => $model->getId(),
                    ));
                    return;
                }
            }
            catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }

        $this->_redirect('*/*');
    }

    public function deleteAction()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $model = Mage::getModel('mf_shippingrule/rule');
                $model->load($id);
                $model->delete();
                $this->_getSession()->addSuccess(
                    $this->_getHelper()->__('Rule was successfully removed.')
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }

        $this->_redirect('*/*');
    }

    public function duplicateAction()
    {
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            try {
                $model = Mage::getModel('mf_shippingrule/rule');
                $model->load($id);
                $storeIds = $model->getStoreIds();
                if ($storeIds) {
                    foreach ($storeIds as $storeId) {
                        $model->addStoreId($storeId);
                    }
                }
                $model->setId(null);
                $model->save();
                $this->_getSession()->addSuccess(
                    $this->_getHelper()->__('Rule was successfully duplicated. You can edit it below.')
                );
                $this->_redirect('*/*/edit', array('id' => $model->getId()));
                return;
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
                Mage::logException($e);
                $this->_redirect('*/*/edit', array('id' => $id));
                return;
            }
        }

        $this->_redirect('*/*');
    }

    public function newConditionHtmlAction()
    {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];
        $model = Mage::getModel($type)
            ->setId($id)
            ->setType($type)
            ->setRule(Mage::getModel('mf_shippingrule/rule'))
            ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

    public function massDeleteAction()
    {
        $ruleIds = $this->getRequest()->getPost('rule_ids', array());
        $deletedRules = 0;
        $rules = Mage::getModel('mf_shippingrule/rule')->getCollection()
            ->addFieldToFilter('rule_id', array('in' => $ruleIds));
        foreach ($rules as $rule) {
            $rule->delete();
            ++$deletedRules;
        }
        $this->_getSession()->addSuccess($this->__('%s rule(s) have been deleted.', $deletedRules));
        $this->_redirect('*/*/');
    }

    public function massEnableAction()
    {
        $ruleIds = $this->getRequest()->getPost('rule_ids', array());
        $enabledRules = 0;
        $rules = Mage::getModel('mf_shippingrule/rule')->getCollection()
            ->addFieldToFilter('rule_id', array('in' => $ruleIds));
        foreach ($rules as $rule) {
            $rule->setIsActive(true);
            $rule->save();
            ++$enabledRules;
        }
        $this->_getSession()->addSuccess($this->__('%s rule(s) have been enabled.', $enabledRules));
        $this->_redirect('*/*/');
    }

    public function massDisableAction()
    {
        $ruleIds = $this->getRequest()->getPost('rule_ids', array());
        $disabledRules = 0;
        $rules = Mage::getModel('mf_shippingrule/rule')->getCollection()
            ->addFieldToFilter('rule_id', array('in' => $ruleIds));
        foreach ($rules as $rule) {
            $rule->setIsActive(false);
            $rule->save();
            ++$disabledRules;
        }
        $this->_getSession()->addSuccess($this->__('%s rule(s) have been disabled.', $disabledRules));
        $this->_redirect('*/*/');
    }

    public function exportCsvAction()
    {
        $io = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = 'mf_shippingrule_' . md5(microtime());
        $file = $path . DS . $name . '.csv';

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $ruleId = $this->getRequest()->getParam('rule_id', false);
        if ($ruleId === false) {
            $ruleIds = $this->getRequest()->getPost('rule_ids', array());
        } else {
            $ruleIds = array($ruleId);
        }
        
        $collection = Mage::getModel('mf_shippingrule/rule')->getCollection();
        if (!empty($ruleIds)) {
            $collection->addFieldToFilter('rule_id', array('in' => $ruleIds));
        }

        $rule = $collection->getFirstItem();
        if ($rule) {
            $row = $rule->getData();
            unset($row['rule_id']);
            $headers = array_keys($row);
            $headers[] = 'store_ids';
            $io->streamWriteCsv($headers);
        }

        foreach ($collection as $rule) {
            $row = $rule->getData();
            $row['store_ids'] = implode(',', $rule->getStoreIds());
            unset($row['rule_id']);
            $io->streamWriteCsv($row);
        }

        $io->streamUnlock();
        $io->streamClose();

        $this->_prepareDownloadResponse('rules.csv', array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        ));
    }

    public function exportXmlAction()
    {
        $io = new Varien_Io_File();

        $path = Mage::getBaseDir('var') . DS . 'export' . DS;
        $name = 'mf_shippingrule_' . md5(microtime());
        $file = $path . DS . $name . '.xml';

        $io->setAllowCreateFolders(true);
        $io->open(array('path' => $path));
        $io->streamOpen($file, 'w+');
        $io->streamLock(true);

        $ruleId = $this->getRequest()->getParam('rule_id', false);
        if ($ruleId === false) {
            $ruleIds = $this->getRequest()->getPost('rule_ids', array());
        } else {
            $ruleIds = array($ruleId);
        }
        
        $collection = Mage::getModel('mf_shippingrule/rule')->getCollection();
        if (!empty($ruleIds)) {
            $collection->addFieldToFilter('rule_id', array('in' => $ruleIds));
        }

        $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
        $xml .= '<rules>';
        foreach ($collection as $rule) {
            $row = $rule->getData();
            $row['store_ids'] = implode(',', $rule->getStoreIds());
            unset($row['rule_id']);

            $xml .= '<rule>';
            foreach ($row as $fieldName => $fieldValue) {
                $fieldValue = "<![CDATA[$fieldValue]]>";
                $xml .= "<$fieldName>$fieldValue</$fieldName>";
            }
            $xml .= '</rule>';

        }
        $xml .= '</rules>';
        $doc = new DOMDocument('1.0');
        $doc->preserveWhiteSpace = false;
        $doc->formatOutput = true;
        $doc->loadXML($xml);

        $io->streamWrite($doc->saveXML());
        $io->streamUnlock();
        $io->streamClose();

        $this->_prepareDownloadResponse('rules.xml', array(
            'type'  => 'filename',
            'value' => $file,
            'rm'    => true // can delete file after use
        ));
    }
}