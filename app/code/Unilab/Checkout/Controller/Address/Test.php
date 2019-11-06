<?php
 
namespace Unilab\Checkout\Controller\Address;


class Test extends \Magento\Framework\App\Action\Action
{

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Magento\Shipping\Model\Config $shipconfig
	)
	{
		$this->resultJsonFactory = $resultJsonFactory;
		$this->shipconfig = $shipconfig;
    	$this->scopeConfig = $scopeConfig;

		return parent::__construct($context);
	}

	public function execute()
	{
		$response = $this->resultJsonFactory->create();
		
		$activeCarriers = $this->shipconfig->getActiveCarriers();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
		foreach($activeCarriers as $carrierCode => $carrierModel)
		{
			$options = array();
			if( $carrierMethods = $carrierModel->getAllowedMethods() )
			{
				foreach ($carrierMethods as $methodCode => $method)
				{
					$code= $carrierCode.'_'.$methodCode;
					$options[]=array('value'=>$code,'label'=>$method);

				}
				$carrierTitle =$this->scopeConfig->getValue('carriers/'.$carrierCode.'/title');

			}
			$methods[]=array('value' =>  $options,'label' => $carrierTitle);
		}
		$response->setData($methods);
		 
		return $response;
	}
}