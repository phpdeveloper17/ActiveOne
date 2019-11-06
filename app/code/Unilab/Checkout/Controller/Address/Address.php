<?php
 
namespace Unilab\Checkout\Controller\Address;


class Address extends \Magento\Framework\App\Action\Action
{

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
		\Magento\Customer\Model\AddressFactory $addressFactory
	)
	{
		$this->resultJsonFactory = $resultJsonFactory;
        $this->addressFactory = $addressFactory;
		return parent::__construct($context);
	}

	public function execute()
	{
		$response = $this->resultJsonFactory->create();
		
		try {
			
			$data = $this->getRequest()->getPost();
	
			$address = $this->addressFactory->create()->load($data['id']);
			
			$res = [
				'success' => true,
				'data' => $address->toArray()
			];

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