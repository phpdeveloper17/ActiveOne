<?php
namespace Unilab\Imagecapture\Controller\Index;

class Processing extends \Magento\Framework\App\Action\Action
{
	protected $_pageFactory;
	protected $resultJsonFactory;
	protected $customerSession;
	protected $checkoutSession;
	protected $directoryList;
	protected $resourceConnection;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $pageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\ResourceConnection $resourceConnection
        )
	{
        $this->_pageFactory = $pageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->customerSession = $customerSession;
        $this->checkoutSession = $checkoutSession;
        $this->directoryList = $directoryList;
        $this->resourceConnection = $resourceConnection;
		
		return parent::__construct($context);
	}

	public function execute()
	{
        $result = $this->resultJsonFactory->create();
        $resultPage = $this->_pageFactory->create();
        $post = $this->getRequest()->getPostValue();

        try
			{
			
				$customer 			= $this->customerSession->getCustomer();
				$customer_id 		= $customer->getId();		
				
				if (empty($customer_id)):
					$cus_id = $_SERVER['REMOTE_ADDR'];
				else:
					$cus_id = $customer->getId();						
				endif;						

				$path 				= $this->directoryList->getRoot();
				$customer_rx_dir 	= $path. DIRECTORY_SEPARATOR ."customerlog";			

				if (!file_exists($customer_rx_dir)) {
					mkdir($path. DIRECTORY_SEPARATOR ."customerlog");
				}
				
				//Retrieve data from temp file		
				$file 		= $customer_rx_dir. DIRECTORY_SEPARATOR .$cus_id.'_capturedRx.temp';	
				
				if (!file_exists($file)) 
				{
					$response['message'] 			= 'No Rx found!';
					$response['success'] 			= true;
				}
				else
				{
				
					$current 	= file_get_contents($file);				
					$data_orig = explode('|//|',$current);
					
					//Connect to Database using core resource
					$connection = $this->resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
					$connection->beginTransaction();						
					$qoute_id 	= $this->checkoutSession->getQuoteId(); 				

					foreach ($data_orig as $key=>$value):
					
						if (!empty($value)):
						
							$data_get = json_decode($value);
							
							foreach ($data_get as $key=>$value):
							
								if ($key == 'origimage_name'):
									$orig_name = $value;
								elseif ($key == 'capture_rx'):
									$capture_rx = $value;
								elseif ($key == 'prod_id'):
									$product_id = $value;
								endif;
								
							endforeach;	
						
						//	Save as reference for viewing the order by Members ID only
						
							$fields = array();
							$fields['original_filename']	= 	$orig_name;
							$fields['status']				=	'PENDING_APPROVAL';
							$fields['date_prescribed']		=	date('Y/m/d H:i:s');
							$fields['created_at']			=	date('Y/m/d H:i:s');
							$fields['updated_at']			=	date('Y/m/d H:i:s');		
							
							$connection->insert('unilab_prescription', $fields);
							$connection->commit();
							
						//Get Entity for Specific Order
						
							$result 		 = $connection->raw_fetchRow("SELECT MAX(`prescription_id`) as LastID FROM `unilab_prescription`");
							$prescription_id = $result['LastID'];	
							
						//Update Order Prescription scanned_rx in Database with table name unilab_prescription
						
							$fields = array();
							$fields['scanned_rx'] = $capture_rx;
							$where = $connection->quoteInto('prescription_id =?', $prescription_id);
							$connection->update('unilab_prescription', $fields, $where);
							$connection->commit();	
							
						//Get Entity for Specific Order
						
							$select = $connection->select()->from('sales_flat_quote_item', array('*'))
										->where('quote_id =?',$qoute_id); 
							$rowArray = $connection->fetchRow($select);
							$item_id = $rowArray['item_id'];
							
							//sales_flat_order_item * item_id =2966 quote_item_id=9076 order_id= 1070
							//sales_flat_quote_item * item_id =9076 quote_id=2558				

							$fields = array();
							$fields['prescription_id'] = $prescription_id;
							$where = array();
							$where[] = $connection->quoteInto('quote_id =?', $qoute_id);
							$where[] = $connection->quoteInto('product_id =?', $product_id);
							$connection->update('sales_flat_quote_item', $fields, $where);
							$connection->commit();
							
							
							$fields = array();
							$fields['prescription_id'] = $prescription_id;
							$where = array();
							$where[] = $connection->quoteInto('quote_item_id =?', $item_id);
							$where[] = $connection->quoteInto('product_id =?', $product_id);
							$connection->update('sales_flat_order_item', $fields, $where);
							$connection->commit();
							
						endif;							
						
					endforeach;					

					//Delete data from temp file
					
					$response['message'] 			= 'With Rx.';
					$response['success'] 			= true;
					$response['prescription_id'] 	= $prescription_id;					
				}

				unlink($file);
			
			}
			
			catch (Exception $e)
			{
				$response['error'] 		= $e;
				$response['success'] 	= false;	
			}
		
		

		return $result->setData($response);
	}
}