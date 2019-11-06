<?php

namespace Unilab\Prescription\Block;

class Camera extends \Magento\Framework\View\Element\Template
{

	protected $storeManager;
	protected $assetRepository;

	public function __construct(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Framework\View\Asset\Repository $assetRepository
		)
	{

		$this->storeManager = $storeManager;
		$this->assetRepository = $assetRepository;

		parent::__construct($context);
	}

	public function getStoreManager(){
		return $this->storeManager;
	}

	public function getAssetRepository(){
		return $this->assetRepository;
	}

	
}