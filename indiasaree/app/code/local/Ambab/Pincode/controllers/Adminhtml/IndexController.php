<?php

class Ambab_Pincode_Adminhtml_IndexController extends Mage_Adminhtml_Controller_Action {

 /* public function indexAction()
  {
  	$pincode = Mage::getModel('pincode/pincode')->getCollection()->getData();
	echo "<pre>";
	print_r($pincode);
	echo "</pre>";
  }*/
  public function uploadAction()
    {

        $this->loadLayout();
        // $requestData = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
        // Mage::register('formData', $requestData);
        $this->getLayout()->getBlock('head')->setTitle($this->__('Import Sizes'));

        $this->renderLayout();
//         var_dump(Mage::getSingleton('core/layout')->getUpdate()->getHandles());
// exit("bailing early at ".__LINE__." in ".__FILE__);
    }

    public function saveeAction()
    {

       
        $data = $this->getRequest()->getPost();
    
        if ($data = $this->getRequest()->getPost()) {
            if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {

                $mimes = array('application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv');
                if (!in_array($_FILES['file']['type'], $mimes)) {
                    Mage::getSingleton('adminhtml/session')->addError('Please upload valid CSV file.');
                    $this->_redirectreferer();
                    return;
                }
                try {
                    $path = Mage::getBaseDir() . DS . 'media' . DS . 'ZipCode';
                    if (!file_exists($path)) {
                        mkdir($path, 0777, true);
                    }
                    $currentTimestamp = Mage::getModel('core/date')->timestamp(time());
                    $date = date('Y_m_d_H_i_s_', $currentTimestamp);

                    $fname = $date . $_FILES['file']['name'];
                    $fullname = $path . DS . $fname;
                    $uploader = new Varien_File_Uploader('file');
                    $uploader->setAllowedExtensions(array('CSV', 'csv'));
                    $uploader->setAllowCreateFolders(true);
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);
                    $uploader->save($path, $fname); //save the
                } catch (Exception $e) {
                	echo "herer";
                	exit;
                    Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                    $this->_redirectreferer();
                    return;
                }
            } else {
                Mage::getSingleton('adminhtml/session')->addError('Please select the file to upload');
                $this->_redirectreferer();
                return;
            }
        }

        $this->importData($fullname);

        $this->_redirect('*/*/');

        return;
    }

    protected function importData($filePath)
    {

        if (!file_exists($filePath)) {
            Mage::log("File doesn't exist - " . $filePath, null, 'ZipCode.log');
            return false;
        }

        $resource = Mage::getSingleton('core/resource');
        $writeResource = $resource->getConnection('core_write');
        $tableName = $resource->getTableName('pincode/pincode');
        $connection = Mage::getSingleton('core/resource')->getConnection('core_write');
        $connection->query('TRUNCATE TABLE `pincode`');

        $fileHandle = fopen($filePath, "r");

        $i = 0;
        while (!feof($fileHandle)) {
            $line = fgets($fileHandle);
            $row = explode(",", $line);
            if ($i == 0) {
                $i++;
                continue;
            }
            if (isset($row[1]) && $row[1] != '') {
                $rowRecord = "INSERT INTO $tableName (pincode, status) VALUES (:pincode, :status)";
                
                $bindValues = array(
                    // 'zip_code' => $row[0],
                    'pincode' => $row[0],
                    'status' => $row[1]
                );
                $writeResource->query($rowRecord, $bindValues);
                $i++;
            }
        }

        fclose($fileHandle);

        Mage::getSingleton('adminhtml/session')->addSuccess($i-1 . ' record(s) successfully imported!');

        Mage::log("Imported file - " . $filePath, null, 'Zipcode.log');
    }


    public function massDeleteAction() {

        $requestIds = $this->getRequest()->getParam('zipcodeIds');

        if(!is_array($requestIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select reqeust(s)'));
        } else {
            try {
                foreach ($requestIds as $requestId) {
                    $RequestData = Mage::getModel('pincode/pincode')->load($requestId);                    
                    $RequestData->delete();                    
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($requestIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/');
    }


  	protected function _isAllowed()
{
    return true;
}

	public function indexAction()
	{
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('pincode/adminhtml_container'));
		$this->renderLayout();
	}

	public function newAction()
	{
		$this->_forward('edit');
	}

	public function editAction()
	{
		$id = $this->getRequest()->getParam('id', null);
		$model = Mage::getModel('pincode/pincode');
		if($id)
		{
			$model->load((int) $id);
			if($model->getId())
			{
				$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
				if($data)
				{
					$model->setData($data)->setId($id);
				}
		} else {
			Mage::getSingleton('adminhtml/session')->addError('Does not exist');
			$this->_redirect('*/');
	}
	Mage::register('data', $model);
	}
		$this->loadLayout();
		$this->_addContent($this->getLayout()->createBlock('pincode/adminhtml_container_edit'));
		$this->renderLayout();
	}

	public function saveAction()
	{
		if($data = $this->getRequest()->getPost()){
			$model = Mage::getModel('pincode/pincode');
		try 
		{
			$id = $this->getRequest()->getParam('id');
			$model->setData($data);
			Mage::getSingleton('adminhtml/session')->setFormData($data);
			if($id){ 
				$model->setId($id); 
			}
			$model->save();
			if(!$model->getId()){
				Mage::throwException('Error saving record');
			}
			Mage::getSingleton('adminhtml/session')->addSuccess('Record was successfully saved.');
			Mage::getSingleton('adminhtml/session')->setFormData(false);
			$this->_redirect('*/*/');
			} catch(Exception $e){
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/');
			}
		}
		/*Mage::getSingleton('adminhtml/session')->addError('No data found to save');*/
		$this->_redirect('*/*/');	
	}

	public function deleteAction()
	{
		if($id = $this->getRequest()->getParam('id')){
		try{
			$model = Mage::getModel('pincode/pincode');
			$model->setId($id);
			$model->delete();
			Mage::getSingleton('adminhtml/session')->addSuccess('The record has been deleted.');
			$this->_redirect('*/*/');
		} 
		catch(Exception $e){
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
			$this->_redirect('*/*/edit', array('id' => $id));
		}
		}
		/*Mage::getSingleton('adminhtml/session')->addError('Unable to find the record to delete.');*/
		$this->_redirect('*/*/');
}

	public function viewAction()
	{
		echo "View";
	}




public function exportCsvAction()
    {
        $fileName   = 'pincode.csv';
        $grid       = $this->getLayout()->createBlock('pincode/adminhtml_container_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getCsvFile());
    }

    /**
     *  Export order grid to Excel XML format
     */
    public function exportExcelAction()
    {
        $fileName   = 'pincode.xml';
        $grid       = $this->getLayout()->createBlock('pincode/adminhtml_container_grid');
        $this->_prepareDownloadResponse($fileName, $grid->getExcelFile($fileName));
    }
}

?>