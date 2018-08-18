<?php

class Ambab_Pincode_Block_Adminhtml_Container_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	public function __construct()
		{
			parent::__construct();
			$this->setId('containerGrid');
			$this->setDefaultSort('id');
			$this->setDefaultDir('ASC');
		}

	protected function _prepareCollection()
		{
			$collection = Mage::getModel('pincode/pincode')->getCollection();
			$this->setCollection($collection);
			return parent::_prepareCollection();
		}

	protected function _prepareColumns()
		{
			$this->addColumn('id', array(
			'header' => 'ID',
			'align' => 'right',
			'width' => '50px',
			'index' => 'id'
			));
			
			$this->addColumn('pincode', array(
			'header' => 'Pincode',
			'align' => 'right',
			'width' => '100px',
			'index' => 'pincode'
			));
			
			$this->addColumn('status', array(
			'header' => 'Status',
			'align' => 'right',
			'sortable' => false,
			'type' 	=> 'options',
			'width' => '100px',
			'index' => 'status',
			'options' => array('Not Available', 'Available')
			));

			$this->addColumn('action', array(
   	   		'header' => 'ACTION',
   	   		'width' => '100',
   	   		'sortable' => 'false',
   	   		'is_system' => true,
   	   		'type' => 'action',
   	    	'getter' => 'getId',
   	  	 	'actions' => array(
   	        array('caption' => Mage::helper('pincode')->__('Edit'),
   	               'url' => array('base'=> '*/*/Edit'),
   	               'field' => 'id'
   	           ))
            ));

            
           
            $this->addExportType('*/*/exportCsv', Mage::helper('pincode')->__('CSV'));
        $this->addExportType('*/*/exportExcel', Mage::helper('pincode')->__('Excel XML'));
            // Add additional columns
			return parent::_prepareColumns();
		}
	    
	    public function getRowUrl($row){
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
}

?>