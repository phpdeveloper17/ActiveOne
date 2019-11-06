<?php
/**
 * DigitalCouponing Ascii Index Controller.
 * @category  Unilab
 * @package   Unilab_DigitalCouponing
 * @author    Reyson Aquino
 */
namespace Unilab\DigitalCouponing\Controller\DigitalCoupon;

class Validate extends \Magento\Framework\App\Action\Action
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
        $response = $this->jsonFactory->create();
        $data   = $this->getRequest()->getPost();

        $validCoupon = $this->digitalCoupon->validateInput($data['dcinput']);

        if($validCoupon) :
            $usedCoupon = $this->digitalCoupon->checkExistingCoupon($data['dcinput'], $data['quoteid']);
            if($usedCoupon) :
                $result = 'exist';
            else :
                $result = true;
            endif;
        else :
            $result = false;
        endif;
            

        $response->setData(['result' => $result]);

        return $response;
    }

}
