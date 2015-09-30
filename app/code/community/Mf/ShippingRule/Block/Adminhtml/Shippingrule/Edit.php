<?php

class Mf_ShippingRule_Block_Adminhtml_Shippingrule_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_blockGroup = 'mf_shippingrule';
        $this->_controller = 'adminhtml_shippingrule';

        if ($this->getRule()) {
            $this->_addButton(
                'export_xml',
                array(
                    'label' => Mage::helper('mf_shippingrule')->__('Export XML'),
                    'onclick' => 'setLocation(\''.$this->getExportXmlUrl().'\')',
                )
            );
            $this->_addButton(
                'export_csv',
                array(
                    'label' => Mage::helper('mf_shippingrule')->__('Export CSV'),
                    'onclick' => 'setLocation(\''.$this->getExportCsvUrl().'\')',
                )
            );
        }
        
        if ($this->getRule()->isDuplicable()) {
            $this->_addButton(
                'duplicate',
                array(
                    'label' => Mage::helper('mf_shippingrule')->__('Duplicate'),
                    'onclick' => 'setLocation(\''.$this->getDuplicateUrl().'\')',
                    'class' => 'add',
                )
            );
        }

        $this->_addButton(
            'saveandcontinue', 
            array(
                'label' => Mage::helper('mf_shippingrule')->__('Save and Continue Edit'),
                'onclick' => 'saveAndContinueEdit()',
                'class' => 'save',
            ),
            2
        );

        $this->_formScripts[] = '
            function saveAndContinueEdit(){
                editForm.submit($(\'edit_form\').action+\'back/edit/\');
            }
        ';
    }

    public function getRule()
    {
        return Mage::registry('rule_data');    
    }
    
    public function getHeaderText()
    {
        if ($this->getRule() && $this->getRule()->getId()) {
            return Mage::helper('mf_shippingrule')->__('Edit Rule \'%s\'',
                $this->escapeHtml($this->getRule()->getName())
            );
        } else {
            return Mage::helper('mf_shippingrule')->__('Add Rule');
        }
    }

    public function getExportCsvUrl()
    {
        return $this->getUrl('*/*/exportCsv', array('rule_id' => $this->getRequest()->getParam('id')));
    }
    
    public function getExportXmlUrl()
    {
        return $this->getUrl('*/*/exportXml', array('rule_id' => $this->getRequest()->getParam('id')));
    }
    
    public function getDuplicateUrl()
    {
        return $this->getUrl('*/*/duplicate', array('id' => $this->getRequest()->getParam('id')));
    }
}