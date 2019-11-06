<?php
/**
 * DigitalCouponing Ascii Index Controller.
 * @category  Unilab
 * @package   Unilab_DigitalCouponing
 * @author    Reyson Aquino
 */
namespace Unilab\Checkout\Controller\Checkout;

class Test extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Unilab\Checkout\Model\Purchasecap $purchasecap,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
        
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->purchasecap = $purchasecap;
        $this->orderRepository = $orderRepository;
        parent::__construct($context);
    }

    /**
     * Mapped eBay Order List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $response = $this->jsonFactory->create();
        $order = $this->orderRepository->get(25);
        $response->setData($this->purchasecap->update($order));

        return $response;
    }

}
