<?php 

namespace Unilab\Catalog\Controller\Validate;

class Pcap extends \Magento\Framework\App\Action\Action
{
    protected $resultJsonFactory;
    protected $_checkoutSession;
    protected $_customerSession;

    /**
     * @param Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        parent::__construct($context);
    }

    /**
     * Collect relations data
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute()
    {
        $quote = $this->_checkoutSession->getQuote();
        $grandTotal = $quote->getGrandTotal();

        $resourceConnection = $this->_objectManager->get('\Magento\Framework\App\ResourceConnection');
        $connection = $resourceConnection->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
        $customer = $this->_customerSession->getCustomer();
        //Search entity_id from rra_emp_benefits
        
        $select 	=	$connection->select()->from('rra_emp_benefits', array('*'))
        ->where('entity_id=?',$customer->getId())->where('is_active=?',1);
        $benefits_items 	=	$connection->fetchRow($select);
        
        $limit 				= $benefits_items['purchase_cap_limit'];
        $consumed 			= $benefits_items['consumed'];
        $extension 	= $benefits_items['extension'];
        $available  = 0;
        $available 	= $benefits_items['available'];
        $available += $extension;

        $availablePcapAmount = $available;

        $data = [];
        $data['grandTotal'] = $grandTotal;
        $data['availablePcap'] = $availablePcapAmount;
        $resultJson = $this->resultJsonFactory->create();
        
        return $resultJson->setData($data);
    }

    
}