<?php
namespace Unilab\Benefits\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Checkout\Model\Cart as CustomerCart;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;

class PcapAddToCart implements ObserverInterface
{
    /**
     * @var ManagerInterface
     */
    protected $messageManager;
    /**
     * @var RedirectInterface
     */
    protected $redirect;
    /**
     * @var Cart
     */
    protected $cart;
    
    /**
     * @param ManagerInterface $messageManager
     * @param RedirectInterface $redirect
     * @param CustomerCart $cart
     */
    protected $_objectManager;
    protected $_customerSession;
    protected $_checkoutSession;
    private $responseFactory;
    private $url;


    public function __construct(
        ManagerInterface $messageManager,
        RedirectInterface $redirect,
        CustomerCart $cart,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\App\ResponseFactory $responseFactory,
        \Magento\Framework\UrlInterface $url
    ) {
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_customerSession = $customerSession;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->cart = $cart;
        $this->_checkoutSession = $checkoutSession;
        $this->responseFactory = $responseFactory;
        $this->url = $url;
    }
    /**
     * Validate Cart Before going to checkout
     * - event: controller_action_predispatch_checkout_index_index
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
		
        $this->cart->getQuote()->setHasError(true);
        $this->messageManager->getMessages(true);
        $quote = $this->cart->getQuote();
        $cartTotal = $quote->getGrandTotal();

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

        $available 	= $benefits_items['available'];
        $available += $extension;
       
        
        if ($cartTotal > $available) {
            
            $product_id = $this->_checkoutSession->getLastAddedProductId();
            $cartHelper = $this->_objectManager->create("Magento\Checkout\Helper\Cart");
            $items = $cartHelper->getCart()->getItems();
            foreach ($items as $item) {
                if ($item->getProduct()->getId() == $product_id) {
                    $itemId = $item->getItemId();
                    $cartHelper->getCart()->removeItem($itemId)->save();
                }
            }
            $this->messageManager->addError(
                __('Exceeded purchase cap limit [ Cart Total :'.$cartTotal.'] !')
            );
        }
		
		//$this->_checkoutSession->getQuote()->collectTotals()->save();
		$this->cart->getQuote()->unsTotalsCollectedFlag(true)->collectTotals()->save();
        return $this;
    }
}