<?php
class PayUbiz_PayUbiz_Block_Config_Payubizmerchantid 
    extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{
    protected $_itemRenderer;
    protected $_statusRenderer;

    public function _prepareToRender()
    {
        $this->addColumn('currency', array(
            'label' => $this->__('Currency'),
            'style' => 'width:150px',
            'renderer' => $this->_getRenderer()
        ));
        $this->addColumn('mid', array(
            'label' => $this->__('Merchant Key'),
            'style' => 'width:150px',
        ));

        $this->addColumn('salt', array(
            'label' => $this->__('Salt'),
            'style' => 'width:150px',
        ));

        $this->_addAfter = false;
        $this->_addButtonLabel = $this->__('Add');
    }

    protected function  _getRenderer() 
    {
        if (!$this->_itemRenderer) {
            $this->_itemRenderer = $this->getLayout()->createBlock(
                'payubiz/config_adminhtml_form_field_currency', '',
                array('is_render_to_js_template' => true)
            );
        }
        return $this->_itemRenderer;

    }

   
    protected function _prepareArrayRow(Varien_Object $row)
    {
        $row->setData(
            'option_extra_attr_' . $this->_getRenderer()
                ->calcOptionHash($row->getData('currency')),
            'selected="selected"'
        );

        
    }
}
