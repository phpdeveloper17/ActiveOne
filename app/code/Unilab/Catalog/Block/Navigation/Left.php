<?php

namespace Unilab\Catalog\Block\Navigation;

class Left extends \Magento\Catalog\Block\Navigation 
{
    const CATEGORY_ROOT_LEVEL = 2;
    
    protected $storeManager;
    protected $categoryCollection;
    protected $registry;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\ResourceModel\Category\CollectionFactory $categoryCollection,
        \Magento\Framework\Registry $registry
    )
    {
        $this->storeManager = $storeManager;
        $this->categoryCollection = $categoryCollection;
        $this->registry = $registry;
        parent::__construct($context);
    }

    public function getCategoryNavigationMenu()
    {
        $store_id =  $this->storeManager->getStore()->getId();
        $rootCategoryId = $this->storeManager->getStore($store_id)->getRootCategoryId();
        $categories = $this->categoryCollection->create()
            ->addAttributeToSelect('*')
            ->addIsActiveFilter()
            ->addAttributeToFilter('level', self::CATEGORY_ROOT_LEVEL)
            ->addAttributeToFilter('path', array('like' => "1/{$rootCategoryId}/%"))
            ->load();

        return $categories;
    }

    public function getCurrentCategory()
    {
        if($category = $this->registry->registry('current_category')){
			return $category;
		} 
		return false;

    }

    public function canExpandMenu($category) 
    {
        if($current_category = $this->getCurrentCategory()){ 
			if($current_category->getId() == $category->getId() ||
			   in_array($current_category->getId(),$category->getAllChildren(true))){
				return true; 
			}
		}
		return false;
    }
}