<?php
namespace Unilab\OfflinePayments\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Sales\Model\Order;

class SaveTransactionCOD implements ObserverInterface
{
    /**
     * @var ObjectManagerInterface
     */
    protected $_objectManager;
    protected $_transactionFactory;

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\DB\Transaction $trasactionFactory
    ) {
        $this->_objectManager = $objectManager;
        $this->_transactionFactory = $trasactionFactory;
    }

	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
        
        try{
            $order = $observer->getEvent()->getOrder();
            
            $payment = $order->getPayment();

            // $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testcod.log');
            // $logger = new \Zend\Log\Logger();
            // $logger->addWriter($writer);
            // $logger->info($order->getIncrementId());
            // $logger->info($payment->getMethod());
            if( $payment->getMethod()== 'cashondelivery'){
               
                $payment->setTransactionId(null)
                    ->setParentTransactionId($order->getIncrementId())
                    ->setShouldCloseParentTransaction(false)
                    ->setIsTransactionClosed(false)
                    ->setAdditionalInformation('TransactionNo:'.$order->getIncrementId())
                    ->setStatus(Order::STATE_PROCESSING)
                    ->setIsTransactionPending(false);
            
                $payment->setPreparedMessage('');
                $order->save();
                $invoice = $order->prepareInvoice();
                $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
                $invoice->register();
                $invoice->getOrder()->setIsCustomerNotified(true);
                $invoice->getOrder()->setIsInProcess(true);
                $invoice->getOrder()->addStatusHistoryComment('Notified customer about invoice.')
                            ->setIsCustomerNotified(true);
                $transactionSave = $this->_transactionFactory->addObject($invoice)
                    ->addObject($invoice->getOrder());
                $transactionSave->save();
            }
            
            
        }catch(\Exception $e){
            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testcod.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($e->getMessage());
        }
        return $this;
    }
}