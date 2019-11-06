<?php
/**
 * DigitalCouponing Ascii Index Controller.
 * @category  Unilab
 * @package   Unilab_DigitalCouponing
 * @author    Reyson Aquino
 */
namespace Unilab\DigitalCouponing\Controller\DigitalCoupon;

class CheckItem extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     */
    public function __construct(
        \Unilab\DigitalCouponing\Model\DigitalCoupon $digitalCoupon,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        $this->digitalCoupon = $digitalCoupon;
        $this->jsonFactory = $jsonFactory;
        parent::__construct($context);
    }

    /**
     * Mapped eBay Order List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        $result = $this->jsonFactory->create();
        
        $data = $this->getRequest()->getPost();

        $checkResult = $this->digitalCoupon->checkExistingProduct($data['quoteid'], $data['productid']);

        $result->setData(['result' => $checkResult]);

        return $result;
    }

}
