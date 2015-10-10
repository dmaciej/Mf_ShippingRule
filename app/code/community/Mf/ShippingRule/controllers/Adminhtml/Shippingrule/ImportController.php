<?php

class Mf_ShippingRule_Adminhtml_Shippingrule_ImportController
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
        $maxUploadSize = Mage::helper('importexport')->getMaxUploadSize();
        $this->_getSession()->addNotice(
            $this->__('Total size of uploadable files must not exceed %s', $maxUploadSize)
        );

        $this->_title($this->__('Shipping Rules'))->_title($this->__('Import Rules'));

        $this->_initAction()
            ->_addBreadcrumb(
                $this->_getHelper()->__('Import Rules'),
                $this->_getHelper()->__('Import Rules')
            );

        $this->renderLayout();
    }

    public function importPostAction()
    {
        $data = $this->getRequest()->getPost();
        if (!empty($data)) {
            if (
                !empty($_FILES['file']['name'])
                && !empty($_FILES['file']['tmp_name'])
                && is_uploaded_file($_FILES['file']['tmp_name'])
            ) {
                $totalImported = 0;
                $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);

                switch ($extension) {
                    case 'xml':
                        $xml = simplexml_load_file($_FILES['file']['tmp_name']);
                        if ($xml && isset($xml->rule)) {
                            foreach ($xml->rule as $rule) {
                                $model = Mage::getModel('mf_shippingrule/rule');
                                foreach ($rule->children() as $key => $value) {
                                    if ($key == 'store_ids') {
                                        continue;
                                    }
                                    $model->setData($key, $value);
                                }
                                $storeIds = isset($rule->store_ids) ? $rule->store_ids : '';
                                $storeIds = explode(',', $storeIds);
                                $model->setStoreIds($storeIds);

                                // Serializable fields.
                                $model->setData('payment_method', unserialize($model->getData('payment_method')));
                                $model->setData('conditions', unserialize($model->getData('conditions_serialized'));
                                $model->save();
                                ++$totalImported;
                            }
                        }
                        break;

                    case 'csv':
                        $file = new Varien_File_Csv();
                        $data = $file->getData($_FILES['file']['tmp_name']);
                        $headers = array_shift($data);
                        foreach ($data as $row) {
                            $model = Mage::getModel('mf_shippingrule/rule');
                            foreach ($row as $index => $value) {
                                if (!isset($headers[$index]) || $headers[$index] == 'store_ids') {
                                    break;
                                }
                                $model->setData($headers[$index], $value);
                            }
                            $storeIdCol = array_search('store_ids', $headers);
                            if ($storeIdCol) {
                                $storeIds = explode(',', $row[$storeIdCol]);
                                $model->setStoreIds($storeIds);
                            }
                            // Serializable fields.
                            $model->setData('payment_method', unserialize($model->getData('payment_method')));
                            $model->setData('conditions', unserialize($model->getData('conditions_serialized'));
                            $model->save();
                            ++$totalImported;
                        }
                        break;
                }

                $this->_getSession()->addSuccess(
                    $this->__('Imported rules: %s.', $totalImported)
                );
            } else {
                $this->_getSession()->addError(
                    $this->__('Error while uploading file.')
                );
            }
        } else {
            $this->_getSession()->addError(
                $this->__('Error while uploading file.')
            );
        }

        $this->_redirect('*/*');
    }
}