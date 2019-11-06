<?php
 
namespace Unilab\Checkout\Controller\Address;


class ValidateCod extends \Magento\Framework\App\Action\Action
{

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\AddressFactory $addressFactory,
		\Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
		\Unilab\City\Model\CityFactory $cityFactory,
		\Unilab\Movshipping\Model\ShippingFactory $shippingFactory
	)
	{
		$this->resultJsonFactory = $resultJsonFactory;
		$this->addressFactory = $addressFactory;
		$this->scopeConfig = $scopeConfig;
        $this->cityFactory = $cityFactory;
		$this->shippingFactory = $shippingFactory;

		return parent::__construct($context);
	}

	public function execute()
	{
		$response = $this->resultJsonFactory->create();
		
		try {
			
			$data = $this->getRequest()->getPost();
    
            $address = $this->addressFactory->create()->load($data['id']);
            $regionId = $address->getRegionId();
            $cityName = $address->getCity();

            $city = $this->cityFactory->create()
                                      ->getCollection()
                                      ->addFieldToFilter('main_table.region_id', array('eq' => $regionId))
									  ->addFieldToFilter('name', array('eq' => $cityName))
									  ->getFirstItem();

            $cityId = $city->getCityId();

            $groupsConfig = $this->scopeConfig->getValue('carriers/minimumordervalue/destinations', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
			
			$groups = explode(',', $groupsConfig);

            foreach($groups as $key) {
				
				$groupId = $key;
				$shippingGroup = $this->shippingFactory->create()
													   ->getCollection()
													   ->addFieldToFilter('id', array('eq' => $groupId))
													   ->getFirstItem();
				$citiesList = $shippingGroup->getListOfCities();
				$cities = explode(',', $citiesList);									   
				
				if(in_array($cityId, $cities)) :
					$res = [
						'success' => true,
						'message' => 'found'
					];
					break;
				else :
					$res = [
						'success' => false,
						'message' => 'not found'
					];
				endif;
			}

			$response->setData($res);

        }
        catch(\Exception $e) {

			$res = [
				'success' => false,
				'data' => $e->getMessage()
			];

			$response->setData($res);
			
		}
		 
		return $response;
	}
}