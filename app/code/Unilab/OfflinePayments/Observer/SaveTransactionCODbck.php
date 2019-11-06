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

    /**
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Framework\DB\Transaction $trasactionFactory
    ) {
        $this->_objectManager = $objectManager;
    }

	public function execute(\Magento\Framework\Event\Observer $observer) 
	{
        
        try{
            $this->_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $this->_transactionFactory =  $this->_objectManager->create('\Magento\Framework\DB\Transaction');
            $order = $observer->getEvent()->getOrder();
            
            $payment = $order->getPayment();
            $payment->setTransactionId($order->getId())
                    ->setParentTransactionId($order->getId())
                    ->setShouldCloseParentTransaction(false)
                    ->setIsTransactionClosed(false)
                    ->setAdditionalInformation('TransactionNo:'.$order->getId())
                    ->setStatus(Order::STATE_PROCESSING)
                    ->setIsTransactionPending(false);
            
            $payment->setPreparedMessage('');

            $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/testcod.log');
            $logger = new \Zend\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($payment->getId());
            $logger->info($payment->getIncrementId());

            $order->save();

            if(!$order->canInvoice())
            {
                // throw new \LogicException('Cannot create an invoice');
            }
            $invoice = $order->prepareInvoice();
            if (!$invoice->getTotalQty()) {
                // throw new \LogicException('Cannot create an invoice without products.');
            }
            $invoice->setRequestedCaptureCase(\Magento\Sales\Model\Order\Invoice::CAPTURE_ONLINE);
            $invoice->register();
            
            $invoice->getOrder()->setIsCustomerNotified(true);
            $invoice->getOrder()->setIsInProcess(true);
            $invoice->getOrder()->addStatusHistoryComment('Notified customer about invoice.')
                        ->setIsCustomerNotified(true);
            
            // $this->InvoiceSender($invoice);
            
            $transactionSave = $this->_transactionFactory->addObject($invoice)
                ->addObject($invoice->getOrder());
            
            $transactionSave->save();
        }catch(\Exception $e){
            echo "<pre>";
                print_r($e->getMessage());
            echo "</pre>";
        }
        return $this;
    }
}