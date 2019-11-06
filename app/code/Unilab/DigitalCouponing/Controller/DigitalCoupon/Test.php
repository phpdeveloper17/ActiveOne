<?php
/**
 * DigitalCouponing Ascii Index Controller.
 * @category  Unilab
 * @package   Unilab_DigitalCouponing
 * @author    Reyson Aquino
 */
namespace Unilab\DigitalCouponing\Controller\DigitalCoupon;

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
        \Unilab\DigitalCouponing\Model\UsedcouponFactory $usedCouponFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
        
    ) {
        $this->jsonFactory = $jsonFactory;
        $this->orderRepository = $orderRepository;
        $this->usedCouponFactory = $usedCouponFactory;
        $this->timezone = $timezone;
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
        $dateTime = $this->timezone->scopeTimeStamp();
        $order    = $this->orderRepository->get(25);
        // $data   = $this->getRequest()->getPost();
        $coupons = [];
        foreach($order->getAllVisibleItems() as $item) :
            $usedCoupon = $this->usedCouponFactory->create();
            $usedCoupon->setCouponCode('JRXYDEP');
            $usedCoupon->setSku($item->getSku());
            $usedCoupon->setCustomerEmail('test@gmail.com');
            $usedCoupon->setCreatedDateTime($dateTime);
            $usedCoupon->setOrderId(25);
            $usedCoupon->save();
            $usedCoupon->unsetData();
        endforeach;
        // $validCoupon = $this->digitalCoupon->validateInput($data['dcinput']);

        // if($validCoupon) :
        //     $usedCoupon = $this->digitalCoupon->checkExistingCoupon($data['dcinput'], $data['quoteid']);
        //     if($usedCoupon) :
        //         $result = 'exist';
        //     else :
        //         $result = true;
        //     endif;
        // else :
        //     $result = false;
        // endif;
        

        $response->setData(json_encode($coupons));

        return $response;
    }

}
