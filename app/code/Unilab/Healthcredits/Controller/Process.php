<?php
/**
 * Healthcredits.
 * @category  Unilab
 * @package   Unilab_Healthcredits
 * @author    Kristian Claridad
 */
namespace Unilab\Healthcredits\Controller;

use \Magento\Framework\Validator\Exception;
use \Magento\Sales\Model\Order;

class Process extends \Magento\Framework\App\Action\Action
{

    protected $resultPageFactory;
    protected $_quoteRepository;
    protected $_logger;
    protected $_checkoutSession;
    protected $_registry;

    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Unilab\Healthcredits\Logger\Logger $loggerInteface,
        \Magento\Quote\Api\CartRepositoryInterface $cartRepositoryInterface,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Framework\Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($context);

        $this->_logger = $loggerInteface;
        $this->_messageManager = $context->getMessageManager();
        $this->_quoteRepository = $cartRepositoryInterface;
        $this->_checkoutSession = $checkoutSession;
        $this->_registry = $registry;
        $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $this->_assetRepo = $this->_objectManager->get("\Magento\Framework\View\Asset\Repository");
    }
    
    public function execute()
    {
        return $this->resultPageFactory->create();
    }
    protected function _expireAjax()
    {
        if (!$this->_getCheckout()->getQuote()->hasItems()) {
            $this->getResponse()->setHeader('HTTP/1.1','403 Session Expired');
            exit;
        }
    }
  
    protected function _404()
    {
        $this->_registry->register('healthcredits_forward_nocache', true);
        $this->_forward('defaultNoRoute');
    }
    protected function redirectAction()
    {
        $layout = $this->_view->getLayout();
    	
        $this->_getCheckout()->setPaymentQuoteId($this->_getCheckout()->getQuoteId());
       	$this->getResponse()->setBody($layout->createBlock('Unilab\Healthcredits\Block\Redirect')->toHtml());
        $this->_getCheckout()->unsQuoteId();
        $this->_getCheckout()->unsRedirectUrl();

    }
    protected function reloadOrderItems()
	{
        $session = $this->_getCheckout();
		$order = $this->_objectManager->get('Magento\Sales\Model\Order')->loadByIncrementId($session->getLastRealOrderId());
		if(is_object($order) && $order->getId()){
			$cart = $this->_objectManager->get('Magento\Checkout\Model\Cart');
			$items = $order->getItemsCollection();
			foreach ($items as $item) {
				try {
					$cart->addOrderItem($item,$item->getQty());
				}
				catch (Mage_Core_Exception $e){
					if ($this->_getCheckout()->getUseNotice(true)) {
						$this->_getCheckout()->addNotice($e->getMessage());
					}
					else {
						$this->_getCheckout()->addError($e->getMessage());
					}
				}
				catch (Exception $e) {
					$this->_getCheckout()->addException($e,__('Cannot add the item to shopping cart.'));
				}
			}
			$cart->save();
		}
		unset($order);
    }
    public function hcreditsAction()
    {
				
		$rra_access_code 	= @$_GET['rra_access_code']; 
		$rra_sechash_code 	= @$_GET['rra_sechash_code']; 
		$rra_ordernumber	= @$_GET['rra_ordernumber'];		
		$rra_returnurl 		= @$_GET['rra_returnurl'];
		$rra_amount 		= @$_GET['rra_amount'];	
		
		$mediaurl =$this->_assetRepo->createAsset('images/loader-1.gif')->getUrl();
				
		?>		


			<center>
				<div style="text-align:center; color:#252265; margin-top:15%; width:25%;">
					<img src="<?php echo $mediaurl;?>" />
					<br /><br />
					<b>Please wait..</b>
				</div>
			</center>


			<form action="<?php echo $rra_returnurl; ?>" name="healthcredits" id="healthcredits" method="GET" style="width:100%; display:none;">
				<input type="text" name="rra_amount" value="<?php echo $rra_amount; ?>" readonly />
				<input type="text" name="rra_sechash_code" value="<?php echo $rra_sechash_code; ?>" readonly />
				<input type="text" name="rra_ordernumber" value="<?php echo $rra_ordernumber; ?>" readonly />
				<input type="text" name="rra_trnsnumber" value="" />
				<input type="radio" name="rra_trnscode" value="0"  checked />
				<input type="hidden"  name="rra_access_code" value="<?php echo $rra_access_code; ?>" />
				<button type="submit" name="paymenttrans" />Proceed</button>
			</form>
			
			<script language="JavaScript">
				document.healthcredits.submit();
			</script>

		<?php		

    }	


    /**
     * Get checkout session namespace.
     *
     * @return \Magento\Checkout\Model\Session
     */
    protected function _getCheckout()
    {
        return $this->_checkoutSession;
    }
    public function logDebug($message)
    {
        $this->_logger->debug($message);
    }

    public function logWarning($message)
    {
        $this->_logger->warning($message);
    }

    public function logError($message)
    {
        $this->_logger->error($message);
    }

    public function logFatal($message)
    {
        $this->_logger->critical($message);
    }
    public function getfullUrl(){
        $urlInterface = $this->_objectManager->get('Magento\Framework\UrlInterface');
        return $urlInterface->getCurrentUrl();
    }
    function base_url(){
		$objectManager = \Magento\Framework\App\ObjectManager::getInstance();
		$storeManager = $objectManager->get('\Magento\Store\Model\StoreManagerInterface');
		return $storeManager->getStore()->getBaseUrl();

	}
}
