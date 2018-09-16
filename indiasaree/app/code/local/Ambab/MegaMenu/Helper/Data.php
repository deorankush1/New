<?php
class Ambab_MegaMenu_Helper_Data extends Mage_Core_Helper_Abstract
{
	public function getCategoryImagePath($id){
		$id = str_replace('category-node-', '', $id);
		$category = $this->LoadCategory($id);
		return $category->getCategoryMenuImage();
	}	

	public function getCategoryImageUrl($id){		
		$id = str_replace('category-node-', '', $id);
		$category = $this->LoadCategory($id);
		return $category->getCategoryMenuImageUrl();
	}	

	public function LoadCategory($id){
		$category = Mage::getModel('catalog/category')->load($id);
		return $category;
	}
}
	 