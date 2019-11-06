<?php
namespace Unilab\Imagecapture\Observer;
 
use Magento\Framework\Event\ObserverInterface;
 
class Savetempimg implements ObserverInterface
{
    
    protected $_objectManager;
    protected $coreSession;
    protected $customerSession;
    protected $directoryList;
 
    
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\Session\SessionManagerInterface $coreSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Filesystem\DirectoryList $directoryList
    ) {
        $this->_objectManager = $objectManager;
        $this->coreSession = $coreSession;
        $this->customerSession = $customerSession;
        $this->directoryList = $directoryList;
    }
 
    
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $captured_rx = $this->coreSession->getRxdata();
			
        if(!empty($captured_rx)):
        
            $customer 			= $this->customerSession->getCustomer();
            
            //Writing data in temp_file	
            
            $customer_id = $customer->getId();		
            
            if (empty($customer_id)):
                $cus_id = $_SERVER['REMOTE_ADDR'];
            else:
                $cus_id = $customer->getId();						
            endif;						

            $path = $this->directoryList->getRoot();
            $customer_rx_dir = $path. DIRECTORY_SEPARATOR ."customerlog";			

            if (!file_exists($customer_rx_dir)) {
                mkdir($path. DIRECTORY_SEPARATOR ."customerlog");
            }
                        
            $file 		= $customer_rx_dir. DIRECTORY_SEPARATOR .$cus_id.'_capturedRx.temp';						
            $current 	= file_get_contents($file);
            
            $string_data = json_encode($captured_rx);

            $current .= $string_data.'|//|';
            
            file_put_contents($file, $current);
            
            $this->coreSession->unsRxdata();
            
        endif;
    }
}