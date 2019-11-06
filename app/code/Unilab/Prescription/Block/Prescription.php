<?php

namespace Unilab\Prescription\Block;

class Prescription extends \Magento\Framework\View\Element\Template
{

	

	protected $timezoneInterface;
	protected $filterManager;
	protected $customerModelUrl;
	protected $customerSession;
	protected $prescriptionFactory;
	protected $arr;
	protected $storeManager;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezoneInterface,
		\Magento\Framework\Filter\FilterManager $filterManager,
		\Magento\Customer\Model\Url $customerModelUrl,
		\Magento\Customer\Model\Session $customerSession,
		\Unilab\Prescription\Model\PrescriptionFactory $prescriptionFactory,
		\Magento\Store\Model\StoreManagerInterface $storeManager
		)
	{
		$this->timezoneInterface = $timezoneInterface;
		$this->filterManager = $filterManager;
		$this->customerModelUrl = $customerModelUrl;
		$this->customerSession = $customerSession;
		$this->prescriptionFactory = $prescriptionFactory;
		$this->storeManager = $storeManager;

		parent::__construct($context);
	}

	public function getTypeNew(){
		return \Unilab\Prescription\Model\Prescription::TYPE_NEW;
	}

	public function getTypePhoto(){
		return \Unilab\Prescription\Model\Prescription::TYPE_PHOTO;
	}

	public function getTypeExisting(){
		return \Unilab\Prescription\Model\Prescription::TYPE_EXISTING;
	}

	public function getTimezoneInterface(){
		return $this->timezoneInterface;
	}

	public function getFilterManager(){
		return $this->filterManager;
	}

	public function getCustomerModelUrl(){
		return $this->customerModelUrl;
	}

	public function getCustomerSession(){
		return $this->customerSession;
	}

	public function setProductToCartFormFields($params){
		$this->arr = $params; 
	}

	public function getProductToCartFormFields(){
		return $this->arr;
	}

	public function getPrescriptionCollection(){
		return $this->getPrescriptionFactory()->getCollection()->addFieldToFilter('customer_id', $this->getCustomerSession()->getCustomer()->getId());
	}

	public function getPrescriptionFactory(){
		
		return $this->prescriptionFactory->create();
	}

	public function getStoreManager(){
		return $this->storeManager;
	}

	public function getCameraBlock(){
		return $this->getLayout()->createBlock('Unilab\Prescription\Block\Camera')->setTemplate('Unilab_Prescription::camera_layout/camera_form.phtml')->toHtml();
	}


	
}